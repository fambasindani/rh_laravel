export interface Grade {
  id: number;
  sigle: string;
  nom: string;
  statut: number;
}

export interface Province {
  id: number;
  nom: string;
  statut: number;
}

export interface Territoire {
  id: number;
  id_province: number;
  nom: string;
  statut: number;
}

export interface District {
  id: number;
  id_territoire: number;
  nom: string;
  statut: number;
}

export interface Village {
  id: number;
  id_district: number;
  nom: string;
  statut: number;
}

export interface Fonction {
  id: number;
  nom: string;
  statut: number;
}

export interface Direction {
  id: number;
  sigle: string;
  nom: string;
  statut: number;
}

export interface Agent {
  id: number;
  matricule: string;
  id_village: number;
  id_grade: number;
  id_fonction: number;
  id_direction: number;
  nom: string;
  postnom: string;
  prenom: string;
  sexe: string;
  date_naissance: string;
  email: string;
  telephone: string;
  etat_civil: string;
  statut: number;
  reference_engagement: string;
  date_engagement: string;
  photo: string;
}

export interface Dependant {
 id?: number;
  id_agent: number;
  nom: string;
  postnom: string;
  prenom: string;
  date_naissance: string;
  lieu_naissance: string;
  etat: string;
  relation: string;
  statut: number;
}

export interface Affectation {
  id: number;
  id_agent: number;
  id_direction: number;
  date_debut: string;
  date_fin: string | null;
}

export interface Promotion {
  id: number;
  id_agent: number;
  id_grade: number;
  date_debut: string;
  date_fin: string | null;
  reference: string;
}

export interface Document {
  id: number;
  id_agent: number;
  intitule: string;
  chemin_fichier: string;
}



export interface User {
  id: number;
  name: string;
  email: string;
  password: string; // hash
  role: 'admin' | 'agent';
  statut: number;
}

export interface AgentDetail extends Agent {
  village?: Village;
  district?: District;
  territoire?: Territoire;
  province?: Province;
  grade?: Grade;
  fonction?: Fonction;
  direction?: Direction;
  dependants: Dependant[];
  documents: Document[];
  affectations: Affectation[];
  promotions: Promotion[];
}