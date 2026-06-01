# Discord Webhook pour ClientXCMS

<img src="thumbnail.png" width="250" alt="Discord Webhook WazdL" style="border-radius: 8px; margin: 10px 0;">

**Discord Webhook** est un module gratuit et ultra-léger pour **ClientXCMS**. Il vous permet de connecter instantanément votre panel à votre serveur Discord afin de recevoir des notifications élégantes (embeds) en temps réel sur l'activité de vos clients et de vos services.

---

## ⚡ Fonctionnalités

- **Notifications en temps réel** : Restez informé instantanément à chaque étape clé de la vie de votre panel.
- **Sélection et filtres d'événements** : Activez ou désactivez les notifications individuellement pour chaque type d'événement.
- **Personnalisation esthétique complète** :
  - Nom et avatar du bot personnalisables.
  - Personnalisation du texte et de l'icône du footer.
  - **Sélecteur de couleur d'embed intégré** dans l'admin pour attribuer une couleur spécifique à chaque événement.
- **Mentions configurables** : Ajoutez facilement des mentions (`@everyone`, `@here` ou un ID de rôle) pour alerter les administrateurs sur les événements importants.
- **Aperçu interactif en direct** : Visualisez le rendu exact de l'embed Discord dans l'administration pendant vos modifications.
- **Bouton de test** : Testez votre webhook en un clic pour valider la configuration.

---

## 📅 Événements pris en charge (8 types d'alertes)

1. **🆕 Nouveau client inscrit** : Alerte dès qu'un nouvel utilisateur crée son compte.
2. **🧾 Nouvelle facture créée** : Notifie lors de la génération d'une nouvelle facture.
3. **💰 Paiement reçu** : Alerte immédiate dès qu'une facture est réglée et validée.
4. **🚀 Service créé / livré** : Alerte lors du provisioning d'un service.
5. **⚠️ Service suspendu** : Idéal pour suivre les impayés et suspensions.
6. **✅ Service réactivé** : Alerte automatique dès la réactivation d'un service.
7. **❌ Service expiré / supprimé** : Suivi des résiliations de contrats.
8. **⬆️ Service mis à niveau (Upgrade)** : Alerte lorsqu'un client change d'offre/produit.

---

## ⚙️ Installation

1. Téléchargez ou clonez ce dépôt GitHub.
2. Copiez le dossier du module dans le répertoire `modules/discordwebhookwazdl/` de votre installation ClientXCMS.
3. Allez dans le panel d'administration de ClientXCMS, rubrique **Modules**, et **Activez** le module "Discord Webhook".
4. Rendez-vous dans **Configuration** (ou via le menu de gauche) -> **Discord Webhook**.
5. Créez un webhook sur votre serveur Discord (Paramètres du serveur -> Intégrations -> Webhooks -> Nouveau webhook) et copiez l'URL.
6. Collez l'URL dans la configuration, cliquez sur **Tester le Webhook** pour valider et enregistrez !

---

## 🤝 Auteur & Support

Développé avec ❤️ par la **WazdL Team**. N'hésitez pas à ouvrir une Issue ou à proposer une Pull Request sur le dépôt pour toute suggestion ou bug.

*Module sous licence MIT - Gratuit et Open Source.*
