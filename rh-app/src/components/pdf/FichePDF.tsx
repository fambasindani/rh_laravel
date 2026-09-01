import React from 'react';
import { Page, Text, View, Document, StyleSheet, Image } from '@react-pdf/renderer';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import type { AgentDetail } from '../../services/agent.service';

// Styles pour le PDF
const styles = StyleSheet.create({
  page: {
    padding: 30,
    fontSize: 11,
    fontFamily: 'Helvetica',
  },
  header: {
    flexDirection: 'row',
    marginBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#cccccc',
    paddingBottom: 10,
  },
  logo: {
    width: 80,
    height: 80,
    marginRight: 10,
  },
  title: {
    fontSize: 16,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 12,
    textAlign: 'center',
    color: '#555',
  },
  section: {
    marginBottom: 15,
  },
  sectionTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    backgroundColor: '#f0f0f0',
    padding: 5,
    marginBottom: 10,
  },
  row: {
    flexDirection: 'row',
    marginBottom: 5,
  },
  label: {
    width: '30%',
    fontWeight: 'bold',
  },
  value: {
    width: '70%',
  },
  photo: {
    width: 100,
    height: 100,
    objectFit: 'cover',
    marginTop: 10,
  },
  footer: {
    position: 'absolute',
    bottom: 30,
    left: 30,
    right: 30,
    textAlign: 'center',
    fontSize: 9,
    color: '#777',
    borderTopWidth: 1,
    borderTopColor: '#ccc',
    paddingTop: 5,
  },
});

interface FichePDFProps {
  agent: AgentDetail;
  logoUrl?: string; // URL du logo (sera passé depuis le composant parent)
}

const FichePDF: React.FC<FichePDFProps> = ({ agent, logoUrl }) => {
  // Formatage des dates
  const dateNaissance = format(new Date(agent.date_naissance), 'dd/MM/yyyy', { locale: fr });
  const dateEngagement = format(new Date(agent.date_engagement), 'dd/MM/yyyy', { locale: fr });

  return (
    <Document>
      <Page size="A4" style={styles.page}>
        {/* En-tête avec logo et titre */}
        <View style={styles.header}>
          {logoUrl && <Image style={styles.logo} src={logoUrl} />}
          <View style={{ flex: 1 }}>
            <Text style={styles.title}>RÉPUBLIQUE DÉMOCRATIQUE DU CONGO</Text>
            <Text style={styles.subtitle}>DIRECTION GÉNÉRALE DES RECETTES ADMINISTRATIVES, JUDICIAIRES, DOMANIALES ET DE PARTICIPATIONS</Text>
            <Text style={styles.subtitle}>FICHE D'IDENTIFICATION DE L'AGENT</Text>
          </View>
        </View>

        {/* Photo (si disponible) */}
        {agent.photo && (
          <View style={{ alignItems: 'center', marginBottom: 15 }}>
            <Image style={styles.photo} src={agent.photo} />
          </View>
        )}

        {/* Identité */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>IDENTITÉ</Text>
          <View style={styles.row}>
            <Text style={styles.label}>Matricule :</Text>
            <Text style={styles.value}>{agent.matricule}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Nom :</Text>
            <Text style={styles.value}>{agent.nom} {agent.postnom} {agent.prenom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Sexe :</Text>
            <Text style={styles.value}>{agent.sexe === 'M' ? 'Masculin' : 'Féminin'}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Date de naissance :</Text>
            <Text style={styles.value}>{dateNaissance}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Email :</Text>
            <Text style={styles.value}>{agent.email}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Téléphone :</Text>
            <Text style={styles.value}>{agent.telephone}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>État civil :</Text>
            <Text style={styles.value}>{agent.etat_civil}</Text>
          </View>
        </View>

        {/* Carrière */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>CARRIÈRE</Text>
          <View style={styles.row}>
            <Text style={styles.label}>Grade :</Text>
            <Text style={styles.value}>{agent.grade?.sigle} - {agent.grade?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Fonction :</Text>
            <Text style={styles.value}>{agent.fonction?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Direction :</Text>
            <Text style={styles.value}>{agent.direction?.sigle} - {agent.direction?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Date d'engagement :</Text>
            <Text style={styles.value}>{dateEngagement}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Réf. engagement :</Text>
            <Text style={styles.value}>{agent.reference_engagement}</Text>
          </View>
        </View>

        {/* Localisation */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>LOCALISATION</Text>
          <View style={styles.row}>
            <Text style={styles.label}>Province :</Text>
            <Text style={styles.value}>{agent.province?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Territoire :</Text>
            <Text style={styles.value}>{agent.territoire?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>District :</Text>
            <Text style={styles.value}>{agent.district?.nom}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Village :</Text>
            <Text style={styles.value}>{agent.village?.nom}</Text>
          </View>
        </View>

        {/* Pied de page */}
        <Text style={styles.footer}>
          Document généré le {format(new Date(), 'dd/MM/yyyy à HH:mm')} - Fiche d'identification officielle
        </Text>
      </Page>
    </Document>
  );
};

export default FichePDF;