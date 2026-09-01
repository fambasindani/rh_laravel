import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { agentService } from '../services/agents.service';
import type { Agent } from '../types/agent';
import FormAgent from './FormAgent';
import { useAuth } from '../hooks/useAuth';
import { FormAgentSkeleton } from '../components/ui/Skeleton';

const AgentEdit: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { user } = useAuth();
  const [agent, setAgent] = useState<Agent | null>(null);

  const isOwnProfile = user?.agentId === parseInt(id || '0');

  useEffect(() => {
    if (id) {
      const loadAgent = async () => {
        const fetched = isOwnProfile
          ? await agentService.getProfile()
          : await agentService.getAgentById(parseInt(id));
        setAgent(fetched);
      };
      loadAgent();
    }
  }, [id, isOwnProfile]);

  const mapAgentToInitialValues = (agent: Agent) => ({
    id: agent.id,
    matricule: agent.matricule,
    nom: agent.nom,
    postnom: agent.postnom,
    prenom: agent.prenom,
    sexe: agent.sexe as 'M' | 'F',
    date_naissance: agent.dateNaissance,
    email: agent.email,
    telephone: agent.telephone,
    etat_civil: agent.etatCivil,
    date_engagement: agent.dateEngagement,
    reference_engagement: agent.referenceEngagement,
    id_grade: agent.grade?.id,
    id_fonction: agent.fonction?.id,
    id_direction: agent.direction?.id,
    province: { id: 0, nom: agent.province },
    territoire: { id: 0, nom: agent.territoire },
    village: { id: 0, nom: agent.village },
    photo: agent.photo,
  });

  const handleSuccess = () => {
    navigate('/agents');
  };

  if (!agent) return <FormAgentSkeleton />;

  return (
    <div className="p-6">
      <FormAgent
        initialValues={mapAgentToInitialValues(agent)}
        onSuccess={handleSuccess}
        onCancel={() => navigate('/agents')}
        isOwnProfile={isOwnProfile}
      />
    </div>
  );
};

export default AgentEdit;