<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417213730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE information_personnele (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(300) NOT NULL, prenom VARCHAR(300) NOT NULL, sexe VARCHAR(300) NOT NULL, taille VARCHAR(300) NOT NULL, poids VARCHAR(300) NOT NULL, maladie VARCHAR(300) NOT NULL, num_tel VARCHAR(300) NOT NULL, adresse VARCHAR(300) NOT NULL, INDEX user_id (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (idp INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, nomp INT NOT NULL, img INT NOT NULL, pt INT NOT NULL, PRIMARY KEY(idp)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, categorie_produit VARCHAR(40) NOT NULL, image VARCHAR(40) NOT NULL, nom VARCHAR(40) NOT NULL, prix INT NOT NULL, quantite INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program (id_p INT AUTO_INCREMENT NOT NULL, titre VARCHAR(40) NOT NULL, niveau VARCHAR(40) NOT NULL, description VARCHAR(40) NOT NULL, prix INT NOT NULL, PRIMARY KEY(id_p)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id_reservation INT AUTO_INCREMENT NOT NULL, type_reservation VARCHAR(40) NOT NULL, username VARCHAR(40) NOT NULL, email VARCHAR(250) NOT NULL, phone INT NOT NULL, PRIMARY KEY(id_reservation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id_review INT AUTO_INCREMENT NOT NULL, nbr_star INT NOT NULL, description VARCHAR(40) NOT NULL, id_event INT NOT NULL, PRIMARY KEY(id_review)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id_seance INT AUTO_INCREMENT NOT NULL, type_seance VARCHAR(40) NOT NULL, duree_seance INT NOT NULL, nb_maximal INT NOT NULL, categorie VARCHAR(200) NOT NULL, PRIMARY KEY(id_seance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE information_personnele ADD CONSTRAINT FK_8D1F5773A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE code_promo');
        $this->addSql('ALTER TABLE categories CHANGE id_cat id_cat INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id_cat)');
        $this->addSql('ALTER TABLE client CHANGE id_c id_c INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id_c)');
        $this->addSql('ALTER TABLE commande CHANGE idc idc INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (idc)');
        $this->addSql('ALTER TABLE evenement CHANGE id_event id_event INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id_event)');
        $this->addSql('ALTER TABLE fidelite MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON fidelite');
        $this->addSql('ALTER TABLE fidelite ADD id_c INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE fidelite ADD PRIMARY KEY (id_c, montant)');
        $this->addSql('ALTER TABLE user CHANGE adresse role VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE code_promo (user_id INT NOT NULL, code INT NOT NULL, date_exp DATE NOT NULL, utilise INT NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE information_personnele DROP FOREIGN KEY FK_8D1F5773A76ED395');
        $this->addSql('DROP TABLE information_personnele');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE categories MODIFY id_cat INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON categories');
        $this->addSql('ALTER TABLE categories CHANGE id_cat id_cat INT NOT NULL');
        $this->addSql('ALTER TABLE client MODIFY id_c INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON client');
        $this->addSql('ALTER TABLE client CHANGE id_c id_c INT NOT NULL');
        $this->addSql('ALTER TABLE commande MODIFY idc INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON commande');
        $this->addSql('ALTER TABLE commande CHANGE idc idc INT NOT NULL');
        $this->addSql('ALTER TABLE evenement MODIFY id_event INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON evenement');
        $this->addSql('ALTER TABLE evenement CHANGE id_event id_event INT NOT NULL');
        $this->addSql('ALTER TABLE fidelite ADD id INT AUTO_INCREMENT NOT NULL, DROP id_c, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user CHANGE role adresse VARCHAR(255) NOT NULL');
    }
}
