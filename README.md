# Zinet Eddar

Bienvenue sur le repository du projet **Zinet Eddar**. Il s'agit d'une application web complète comprenant un backend robuste et un frontend moderne.

## 🚀 Présentation du projet
Zinet Eddar est une plateforme développée pour gérer efficacement les opérations requises par le client. Ce projet intègre une architecture découplée avec une API côté backend et une interface utilisateur réactive côté frontend.

## 🛠️ Technologies
- **Backend** : Laravel (PHP)
- **Frontend** : Vue.js 3, Vite, TailwindCSS (selon la configuration)
- **Base de données** : MySQL
- **Stockage de médias** : Cloudinary
- **Outils** : Git, Composer, npm

## 📂 Structure du projet
Le repository est divisé en deux parties principales :
- `backend/` : Contient l'application Laravel (API, logique métier, base de données).
- `frontend/` : Contient l'application Vue.js (interface utilisateur, composants, services).

## ⚙️ Installation locale
Pour lancer le projet en local, assurez-vous d'avoir installé PHP, Composer, Node.js, npm, et un serveur MySQL (ex: XAMPP, WAMP).

1. Clonez le repository :
   ```bash
   git clone https://github.com/WaliMahdi/Zinet-dar.git
   cd Zinet-dar
   ```

2. Installez les dépendances du backend :
   ```bash
   cd backend
   composer install
   ```

3. Installez les dépendances du frontend :
   ```bash
   cd ../frontend
   npm install
   ```

## 🔐 Configuration `.env`
Le projet utilise des variables d'environnement pour sécuriser les données sensibles. **Les fichiers `.env` ne sont jamais poussés sur GitHub.**

### Backend (`backend/.env`)
Copiez le fichier d'exemple et configurez-le :
```bash
cp .env.example .env
```
Assurez-vous de générer la clé d'application :
```bash
php artisan key:generate
```

### Frontend (`frontend/.env`)
Créez un fichier `.env` dans le dossier frontend et ajoutez :
```env
VITE_API_URL=http://127.0.0.1:8000/api
VITE_STORAGE_URL=http://127.0.0.1:8000/storage
VITE_APP_NAME="Zinet Eddar"
```

## 🗄️ Configuration MySQL
1. Créez une base de données MySQL nommée (par exemple `sedi_electro` ou selon votre choix).
2. Mettez à jour le fichier `backend/.env` avec vos identifiants MySQL :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```
3. Lancez les migrations :
```bash
php artisan migrate
```

## ☁️ Configuration Cloudinary
Pour l'upload d'images, le backend utilise Cloudinary. Configurez les variables suivantes dans `backend/.env` :
```env
CLOUDINARY_CLOUD_NAME=votre_cloud_name
CLOUDINARY_API_KEY=votre_api_key
CLOUDINARY_API_SECRET=votre_api_secret
```

## 🏃‍♂️ Lancement des serveurs

### Lancement Laravel (Backend)
Dans le dossier `backend` :
```bash
php artisan serve
```
Le backend sera disponible sur `http://127.0.0.1:8000`.

### Lancement Vue (Frontend)
Dans le dossier `frontend` :
```bash
npm run dev
```
Le frontend sera disponible sur l'URL affichée par Vite (généralement `http://localhost:5173`).
