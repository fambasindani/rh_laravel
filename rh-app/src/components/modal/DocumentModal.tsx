import React, { useState, useRef, useEffect } from 'react';
import Modal from '../ui/Modal';
import Input from '../ui/Input';
import Button from '../Button';
import { FaFile, FaImage, FaFilePdf, FaTrash, FaSpinner, FaCamera } from 'react-icons/fa';
import { PDFDocument } from 'pdf-lib';

const SCANNER_SERVER_URL = 'http://localhost:3001';

export interface DocumentDto {
  id: number;
  intitule: string;
  cheminFichier: string;
}

export interface DocumentPayload {
  idAgent: number;
  intitule: string;
  fichier?: File;
}

interface DocumentModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave: (data: DocumentPayload) => void;
  document?: DocumentDto;
  agentId: number;
}

const DocumentModal: React.FC<DocumentModalProps> = ({ isOpen, onClose, onSave, document, agentId }) => {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [intitule, setIntitule] = useState<string>(document?.intitule || '');
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(null);
  const [errors, setErrors] = useState<{ intitule?: string; fichier?: string }>({});
  const [scanning, setScanning] = useState(false);
  const [scanError, setScanError] = useState<string | null>(null);
  const [scannedPages, setScannedPages] = useState<string[]>([]);
  const [scanProgress, setScanProgress] = useState<string>('');

  useEffect(() => {
    if (fileInputRef.current) fileInputRef.current.value = '';
  }, [isOpen]);

  useEffect(() => {
    return () => { if (preview) URL.revokeObjectURL(preview); };
  }, [preview]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const selectedFile = e.target.files?.[0] ?? null;
    if (!selectedFile) return;
    if (!selectedFile.type.startsWith('image/') && selectedFile.type !== 'application/pdf') {
      setErrors(prev => ({ ...prev, fichier: 'Seuls les images et PDF sont autorises' }));
      if (fileInputRef.current) fileInputRef.current.value = '';
      return;
    }
    if (preview) URL.revokeObjectURL(preview);
    const objectUrl = URL.createObjectURL(selectedFile);
    setFile(selectedFile);
    setScannedPages([]);
    setPreview(selectedFile.type.startsWith('image/') ? objectUrl : null);
    setErrors(prev => ({ ...prev, fichier: undefined }));
  };

  const handleRemoveFile = () => {
    if (preview) URL.revokeObjectURL(preview);
    setFile(null);
    setPreview(null);
    setScannedPages([]);
    setErrors(prev => ({ ...prev, fichier: undefined }));
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const fusePagesToPdf = async (pages: string[]): Promise<File> => {
    const pdf = await PDFDocument.create();
    for (const base64 of pages) {
      const imageBytes = Uint8Array.from(atob(base64), c => c.charCodeAt(0));
      const image = await pdf.embedPng(imageBytes);
      const pdfPage = pdf.addPage([image.width, image.height]);
      pdfPage.drawImage(image, { x: 0, y: 0, width: image.width, height: image.height });
    }
    const pdfBytes = await pdf.save();
    const blob = new Blob([new Uint8Array(pdfBytes)], { type: 'application/pdf' });
    return new File([blob], 'scan_' + new Date().toISOString().slice(0, 10) + '.pdf', { type: 'application/pdf' });
  };

  const handleScanMulti = async () => {
    setScanning(true);
    setScanError(null);
    setScanProgress('Demarrage du scan...');
    try {
      const response = await fetch(SCANNER_SERVER_URL + '/scan-multi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ dpi: 200, maxPages: 20 }),
      });
      const result = await response.json();
      if (!result.success) {
        setScanError(result.message || 'Erreur lors du scan');
        setScanProgress('');
        return;
      }
      const pages: string[] = result.pages.map((p: any) => p.data);
      setScannedPages(pages);
      setScanProgress('Conversion en PDF...');
      const pdfFile = await fusePagesToPdf(pages);
      if (preview) URL.revokeObjectURL(preview);
      const objectUrl = URL.createObjectURL(pdfFile);
      setFile(pdfFile);
      setPreview(objectUrl);
      setErrors(prev => ({ ...prev, fichier: undefined }));
      setScanProgress('Scan termine : ' + pages.length + ' page(s) fusionnees en PDF');
    } catch (err: any) {
      if (err?.message?.includes('Failed to fetch') || err?.message?.includes('NetworkError')) {
        setScanError('Scanner Bridge Server non demarre. Lancez "npm start" dans scanner-server/');
      } else {
        setScanError('Erreur: ' + (err?.message || 'Impossible de scanner'));
      }
      setScanProgress('');
    } finally {
      setScanning(false);
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const newErrors: typeof errors = {};
    if (!intitule.trim()) newErrors.intitule = "L'intitule est obligatoire";
    if (!document && !file) newErrors.fichier = 'Veuillez selectionner un fichier';
    setErrors(newErrors);
    if (Object.keys(newErrors).length > 0) return;
    onSave({ idAgent: agentId, intitule, fichier: file || undefined });
    onClose();
  };

  return (
    <Modal key={document?.id || 'new'} isOpen={isOpen} onClose={onClose} title={document ? 'Modifier document' : 'Ajouter document'} size="lg">
      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <Input label="Intitule" value={intitule} onChange={(e) => setIntitule(e.target.value)} required className={errors.intitule ? 'border border-red-500' : ''} />
          {errors.intitule && <p className="text-red-500 text-sm mt-1">{errors.intitule}</p>}
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
          <div className="flex items-center space-x-2">
            <input type="file" ref={fileInputRef} onChange={handleFileChange} accept="image/*,.pdf"
              className={"block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 " + (errors.fichier ? 'border border-red-500' : '')} />
            {file && (
              <Button type="button" variant="secondary" size="sm" onClick={handleRemoveFile}>
                <FaTrash className="text-red-500" />
              </Button>
            )}
          </div>

          <div className="mt-3">
            <Button type="button" variant="outline" size="sm" onClick={handleScanMulti} disabled={scanning}
              className="inline-flex items-center gap-2 text-blue-600 border-blue-300 hover:bg-blue-50">
              {scanning ? (<><FaSpinner className="animate-spin" /> Scan en cours...</>) : (<><FaCamera className="h-4 w-4" /> Scanner via imprimante</>)}
            </Button>
          </div>

          {scanProgress && !scanError && <p className="text-blue-600 text-sm mt-2 bg-blue-50 p-2 rounded">{scanProgress}</p>}
          {scanError && <p className="text-red-500 text-sm mt-2 bg-red-50 p-2 rounded">{scanError}</p>}
          {errors.fichier && <p className="text-red-500 text-sm mt-1">{errors.fichier}</p>}
        </div>

        {scannedPages.length > 0 && (
          <div className="mt-3">
            <p className="text-sm font-medium text-gray-700 mb-2">Pages scannees ({scannedPages.length})</p>
            <div className="flex flex-wrap gap-2">
              {scannedPages.map((page, idx) => (
                <div key={idx} className="relative">
                  <img src={"data:image/png;base64," + page} alt={"Page " + (idx + 1)} className="h-20 w-16 object-cover rounded border border-gray-200" />
                  <span className="absolute -top-1 -right-1 bg-blue-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{idx + 1}</span>
                </div>
              ))}
            </div>
          </div>
        )}

        {preview && file?.type === 'application/pdf' && (
          <div className="mt-2">
            <p className="text-sm text-gray-500 mb-1">Apercu PDF :</p>
            <embed src={preview} type="application/pdf" width="100%" height="300" className="rounded-lg border border-gray-200" />
          </div>
        )}

        {preview && file?.type.startsWith('image/') && (
          <div className="mt-2">
            <p className="text-sm text-gray-500 mb-1">Apercu :</p>
            <img src={preview} alt="Apercu" className="max-h-40 rounded-lg border border-gray-200" />
          </div>
        )}

        <div className="flex justify-end space-x-2 pt-4">
          <Button type="button" variant="outline" onClick={onClose}>Annuler</Button>
          <Button type="submit" variant="primary">Enregistrer</Button>
        </div>
      </form>
    </Modal>
  );
};

export default DocumentModal;
