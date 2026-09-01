import { agents, grades, fonctions, directions, villages, districts, territoires, provinces, dependants, affectations, promotions, documents } from '../types/database';;

import type { Agent, Grade, Fonction, Direction, Village, District, Territoire, Province, Dependant, Affectation, Promotion, Document } from '../types/data';

// Interface représentant un agent avec toutes ses informations liées (jointures)
export interface AgentDetail extends Agent {
  grade?: Grade;
  fonction?: Fonction;
  direction?: Direction;
  village?: Village;
  district?: District;
  territoire?: Territoire;
  province?: Province;
  dependants: Dependant[];
  affectations: Affectation[];
  promotions: Promotion[];
  documents: Document[];
}

/**
 * Récupère tous les agents avec leurs détails (grade, fonction, direction, localisation, dépendants, etc.)
 * Effectue les jointures nécessaires entre les différents tableaux de données.
 */
export function getAllAgentsWithDetails(): AgentDetail[] {
  return agents.map(agent => {
    // Récupération des entités liées par les identifiants
    const grade = grades.find(g => g.id === agent.id_grade);
    const fonction = fonctions.find(f => f.id === agent.id_fonction);
    const direction = directions.find(d => d.id === agent.id_direction);
    const village = villages.find(v => v.id === agent.id_village);
    
    // Remonter la hiérarchie géographique (district -> territoire -> province)
    const district = village ? districts.find(d => d.id === village.id_district) : undefined;
    const territoire = district ? territoires.find(t => t.id === district.id_territoire) : undefined;
    const province = territoire ? provinces.find(p => p.id === territoire.id_province) : undefined;

    // Récupération des listes liées à l'agent
    const agentDependants = dependants.filter(d => d.id_agent === agent.id);
    const agentAffectations = affectations.filter(a => a.id_agent === agent.id);
    const agentPromotions = promotions.filter(p => p.id_agent === agent.id);
    const agentDocuments = documents.filter(d => d.id_agent === agent.id);

    return {
      ...agent,
      grade,
      fonction,
      direction,
      village,
      district,
      territoire,
      province,
      dependants: agentDependants,
      affectations: agentAffectations,
      promotions: agentPromotions,
      documents: agentDocuments,
    };
  });
}

/**
 * Récupère un agent spécifique par son identifiant avec tous ses détails.
 * @param id - L'identifiant de l'agent
 * @returns L'agent détaillé ou undefined si non trouvé
 */
export function getAgentById(id: number): AgentDetail | undefined {
  const agent = agents.find(a => a.id === id);
  if (!agent) return undefined;

  // Même logique de jointure que ci-dessus, mais pour un seul agent
  const grade = grades.find(g => g.id === agent.id_grade);
  const fonction = fonctions.find(f => f.id === agent.id_fonction);
  const direction = directions.find(d => d.id === agent.id_direction);
  const village = villages.find(v => v.id === agent.id_village);
  const district = village ? districts.find(d => d.id === village.id_district) : undefined;
  const territoire = district ? territoires.find(t => t.id === district.id_territoire) : undefined;
  const province = territoire ? provinces.find(p => p.id === territoire.id_province) : undefined;

  const dependantsList = dependants.filter(d => d.id_agent === agent.id);
  const affectationsList = affectations.filter(a => a.id_agent === agent.id);
  const promotionsList = promotions.filter(p => p.id_agent === agent.id);
  const documentsList = documents.filter(d => d.id_agent === agent.id);

  

  return {
    ...agent,
    grade,
    fonction,
    direction,
    village,
    district,
    territoire,
    province,
    dependants: dependantsList,
    affectations: affectationsList,
    promotions: promotionsList,
    documents: documentsList,
  };
}