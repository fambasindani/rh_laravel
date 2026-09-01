import api from "../types/api";

export interface Grade {
  id: number;
  sigle: string;
  nom: string;
  statut: boolean;
}

export const gradeService = {
  async getAll(): Promise<Grade[]> {
    const response = await api.get('/grades/all');
    return response.data;
  }
};


