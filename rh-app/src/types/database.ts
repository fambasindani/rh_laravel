import type {
  Grade, Province, Territoire, District, Village,
  Fonction, Direction, Agent, Dependant, Affectation,
  Promotion, Document, User
} from '../types/data';

export const grades: Grade[] = [
  { id: 1, sigle: "A1", nom: "Grade A1", statut: 1 },
  { id: 2, sigle: "B1", nom: "Grade B1", statut: 1 },
  { id: 3, sigle: "C1", nom: "Grade C1", statut: 1 }
];

export const provinces: Province[] = [
  { id: 1, nom: "Kinshasa", statut: 1 },
  { id: 2, nom: "Kongo Central", statut: 1 },
  { id: 3, nom: "Haut-Katanga", statut: 1 }
];

export const territoires: Territoire[] = [
  { id: 1, id_province: 1, nom: "Mont Amba", statut: 1 },
  { id: 2, id_province: 2, nom: "Matadi", statut: 1 },
  { id: 3, id_province: 3, nom: "Lubumbashi", statut: 1 }
];

export const districts: District[] = [
  { id: 1, id_territoire: 1, nom: "Lemba", statut: 1 },
  { id: 2, id_territoire: 2, nom: "Nzanza", statut: 1 },
  { id: 3, id_territoire: 3, nom: "Kamalondo", statut: 1 }
];

export const villages: Village[] = [
  { id: 1, id_district: 1, nom: "Salongo", statut: 1 },
  { id: 2, id_district: 2, nom: "Village 2", statut: 1 },
  { id: 3, id_district: 3, nom: "Village 3", statut: 1 }
];

export const fonctions: Fonction[] = [
  { id: 1, nom: "Informaticien", statut: 1 },
  { id: 2, nom: "Comptable", statut: 1 },
  { id: 3, nom: "Secrétaire", statut: 1 }
];

export const directions: Direction[] = [
  { id: 1, sigle: "DIR-IT", nom: "Direction Informatique", statut: 1 },
  { id: 2, sigle: "DIR-FIN", nom: "Direction Financière", statut: 1 },
  { id: 3, sigle: "DIR-ADM", nom: "Direction Administrative", statut: 1 }
];

export const agents: Agent[] = [
  {
    id: 1,
    matricule: "AG001",
    id_village: 1,
    id_grade: 1,
    id_fonction: 1,
    id_direction: 1,
    nom: "Famba",
    postnom: "Ngoy",
    prenom: "Junior",
    sexe: "M",
    date_naissance: "1995-05-10",
    email: "famba@example.com",
    telephone: "0990000000",
    etat_civil: "celibataire",
    statut: 1,
    reference_engagement: "REF001",
    date_engagement: "2020-01-01",
    photo: "photos/famba.jpg"
  },
  {
    id: 2,
    matricule: "AG002",
    id_village: 2,
    id_grade: 2,
    id_fonction: 2,
    id_direction: 2,
    nom: "Kabamba",
    postnom: "Jean",
    prenom: "Paul",
    sexe: "M",
    date_naissance: "1990-03-15",
    email: "kabila@example.com",
    telephone: "0980000000",
    etat_civil: "marie",
    statut: 1,
    reference_engagement: "REF002",
    date_engagement: "2018-06-10",
    photo: "photos/kabila.jpg"
  },
  {
    id: 3,
    matricule: "AG003",
    id_village: 3,
    id_grade: 3,
    id_fonction: 3,
    id_direction: 3,
    nom: "Mukendi",
    postnom: "Grace",
    prenom: "Anne",
    sexe: "F",
    date_naissance: "1998-11-20",
    email: "mukendi@example.com",
    telephone: "0970000000",
    etat_civil: "celibataire",
    statut: 1,
    reference_engagement: "REF003",
    date_engagement: "2021-09-01",
    photo: "photos/mukendi.jpg"
  }
];

export const dependants: Dependant[] = [
  {
    id: 1,
    id_agent: 1,
    nom: "Marie",
    postnom: "Ngoy",
    prenom: "Anna",
    date_naissance: "2000-01-01",
    lieu_naissance: "Kinshasa",
    etat: "vivant",
    relation: "epouse",
    statut: 1
  },
  {
    id: 2,
    id_agent: 2,
    nom: "Pauline",
    postnom: "Jean",
    prenom: "Junior",
    date_naissance: "2010-05-05",
    lieu_naissance: "Matadi",
    etat: "vivant",
    relation: "enfant",
    statut: 1
  },
  {
    id: 3,
    id_agent: 3,
    nom: "Joseph",
    postnom: "Mukendi",
    prenom: "Petit",
    date_naissance: "2015-07-07",
    lieu_naissance: "Lubumbashi",
    etat: "vivant",
    relation: "enfant",
    statut: 1
  }
];

export const affectations: Affectation[] = [
  { id: 1, id_agent: 1, id_direction: 1, date_debut: "2020-01-01", date_fin: null },
  { id: 2, id_agent: 2, id_direction: 2, date_debut: "2018-06-10", date_fin: null },
  { id: 3, id_agent: 3, id_direction: 3, date_debut: "2021-09-01", date_fin: null }
];

export const promotions: Promotion[] = [
  { id: 1, id_agent: 1, id_grade: 1, date_debut: "2020-01-01", date_fin: null, reference: "PROMO001" },
  { id: 2, id_agent: 2, id_grade: 2, date_debut: "2019-01-01", date_fin: null, reference: "PROMO002" },
  { id: 3, id_agent: 3, id_grade: 3, date_debut: "2022-01-01", date_fin: null, reference: "PROMO003" }
];

export const documents: Document[] = [
  { id: 1, id_agent: 1, intitule: "Contrat", chemin_fichier: "docs/contrat1.pdf" },
  { id: 2, id_agent: 2, intitule: "Diplôme", chemin_fichier: "docs/diplome.pdf" },
  { id: 3, id_agent: 3, intitule: "Carte identité", chemin_fichier: "docs/id.pdf" }
];

export const users: User[] = [
  { id: 1, name: "Admin", email: "admin@gmail.com", password: "$2y$10$hash1", role: "admin", statut: 1 },
  { id: 2, name: "Agent1", email: "agent1@gmail.com", password: "$2y$10$hash2", role: "agent", statut: 1 },
  { id: 3, name: "Agent2", email: "agent2@gmail.com", password: "$2y$10$hash3", role: "agent", statut: 1 }
];