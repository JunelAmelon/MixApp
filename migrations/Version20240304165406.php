<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304165406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audios (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, ingenieur_id INT DEFAULT NULL, files VARCHAR(255) NOT NULL, dates_ajout DATE NOT NULL, INDEX IDX_9B73D5F619EB6921 (client_id), INDEX IDX_9B73D5F6331C8F6 (ingenieur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audios_projet (id INT AUTO_INCREMENT NOT NULL, id_projet INT NOT NULL, id_audio INT NOT NULL, etat_audio VARCHAR(255) NOT NULL, INDEX IDX_384805EF76222944 (id_projet), INDEX IDX_384805EFF6F877A5 (id_audio), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, id_client INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, audios_projet_id INT DEFAULT NULL, id_projet INT NOT NULL, id_audio VARCHAR(255) NOT NULL, id_user INT NOT NULL, commentaire VARCHAR(255) NOT NULL, date_commentaire DATE NOT NULL, INDEX IDX_67F068BCC18F6F72 (audios_projet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingenieur (id INT AUTO_INCREMENT NOT NULL, id_ing INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, nom_projet VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, etat_projet VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, id_commentaire INT NOT NULL, id_user INT NOT NULL, reponse VARCHAR(255) NOT NULL, date_reponse DATE NOT NULL, INDEX IDX_5FB6DEC77FE2A54B (id_commentaire), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audios ADD CONSTRAINT FK_9B73D5F619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE audios ADD CONSTRAINT FK_9B73D5F6331C8F6 FOREIGN KEY (ingenieur_id) REFERENCES ingenieur (id)');
        $this->addSql('ALTER TABLE audios_projet ADD CONSTRAINT FK_384805EF76222944 FOREIGN KEY (id_projet) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE audios_projet ADD CONSTRAINT FK_384805EFF6F877A5 FOREIGN KEY (id_audio) REFERENCES audios (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCC18F6F72 FOREIGN KEY (audios_projet_id) REFERENCES audios_projet (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC77FE2A54B FOREIGN KEY (id_commentaire) REFERENCES commentaire (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audios DROP FOREIGN KEY FK_9B73D5F619EB6921');
        $this->addSql('ALTER TABLE audios DROP FOREIGN KEY FK_9B73D5F6331C8F6');
        $this->addSql('ALTER TABLE audios_projet DROP FOREIGN KEY FK_384805EF76222944');
        $this->addSql('ALTER TABLE audios_projet DROP FOREIGN KEY FK_384805EFF6F877A5');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCC18F6F72');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC77FE2A54B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE audios');
        $this->addSql('DROP TABLE audios_projet');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE ingenieur');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
