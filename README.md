# Symfony Context Bundle 🏗️

[🇫🇷 Français](#-français) | [🇬🇧 English](#-english)

## 🇫🇷 Français

Un bundle Symfony qui aide à générer une structure de dossiers pour une architecture DDD (Domain Driven Design).

### Installation 📦

```bash
composer require thedevopser/symfony-context-bundle
```

### Utilisation 🚀

La commande suivante permet de générer un nouveau contexte :

```bash
php bin/console thedevopser:generate:context MonContexte
```

Cette commande va créer la structure de dossiers suivante :

```
MonContexte/
├── Domain/
│   ├── Entity/
│   └── Interfaces/
├── Application/
│   ├── Command/
│   ├── Query/
│   ├── Event/
│   └── Service/
├── Infrastructure/
│   ├── Doctrine/
│   └── Persistence/
├── Presenter/
│   ├── Controller/
│   ├── Form/
│   └── Voter/
└── README.md
```

### Structure 🏛️

- `Domain/` : Le cœur métier
  - `Entity/` : Les entités et value objects
  - `Interfaces/` : Les interfaces des repositories
- `Application/` : La couche application
  - `Command/` : Les commandes et leurs handlers
  - `Query/` : Les requêtes et leurs handlers
  - `Event/` : Les gestionnaires d'événements
  - `Service/` : Les services applicatifs
- `Infrastructure/` : La couche infrastructure
  - `Doctrine/` : L'implémentation des repositories
  - `Persistence/` : Configuration de la persistance
- `Presenter/` : La couche présentation
  - `Controller/` : Les contrôleurs
  - `Form/` : Les types de formulaires
  - `Voter/` : Les voters

---

## 🇬🇧 English

A Symfony bundle that helps generate a folder structure for DDD (Domain Driven Design) architecture.

### Installation 📦

```bash
composer require thedevopser/symfony-context-bundle
```

### Usage 🚀

The following command generates a new context:

```bash
php bin/console thedevopser:generate:context MyContext
```

This command will create the following folder structure:

```
MyContext/
├── Domain/
│   ├── Entity/
│   └── Interfaces/
├── Application/
│   ├── Command/
│   ├── Query/
│   ├── Event/
│   └── Service/
├── Infrastructure/
│   ├── Doctrine/
│   └── Persistence/
├── Presenter/
│   ├── Controller/
│   ├── Form/
│   └── Voter/
└── README.md
```

### Structure 🏛️

- `Domain/`: The business core
  - `Entity/`: Entities and value objects
  - `Interfaces/`: Repository interfaces
- `Application/`: The application layer
  - `Command/`: Commands and their handlers
  - `Query/`: Queries and their handlers
  - `Event/`: Event handlers
  - `Service/`: Application services
- `Infrastructure/`: The infrastructure layer
  - `Doctrine/`: Repository implementations
  - `Persistence/`: Persistence configuration
- `Presenter/`: The presentation layer
  - `Controller/`: Controllers
  - `Form/`: Form types
  - `Voter/`: Voters

---

## Tests 🧪

```bash
vendor/bin/phpunit
```

## Contributing 🤝

Pull requests are welcome! | Les Pull Requests sont les bienvenues !

## License 📄

MIT
