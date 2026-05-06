<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504133815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_3DC1A459A76ED395 (user_id), INDEX IDX_3DC1A4593D865311 (planning_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE shopping_list_item (id INT AUTO_INCREMENT NOT NULL, quantity DOUBLE PRECISION DEFAULT NULL, unit VARCHAR(50) DEFAULT NULL, is_checked TINYINT NOT NULL, updated_at DATETIME NOT NULL, shopping_list_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_4FB1C22423245BF9 (shopping_list_id), INDEX IDX_4FB1C224933FE08C (ingredient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A459A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A4593D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT FK_4FB1C22423245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT FK_4FB1C224933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE ingredient_shop ADD CONSTRAINT FK_F23BFC34933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE ingredient_shop ADD CONSTRAINT FK_F23BFC344D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planning_recipe ADD CONSTRAINT FK_BAA993583D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE planning_recipe ADD CONSTRAINT FK_BAA9935859D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE1359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A459A76ED395');
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A4593D865311');
        $this->addSql('ALTER TABLE shopping_list_item DROP FOREIGN KEY FK_4FB1C22423245BF9');
        $this->addSql('ALTER TABLE shopping_list_item DROP FOREIGN KEY FK_4FB1C224933FE08C');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE shopping_list_item');
        $this->addSql('ALTER TABLE ingredient_shop DROP FOREIGN KEY FK_F23BFC34933FE08C');
        $this->addSql('ALTER TABLE ingredient_shop DROP FOREIGN KEY FK_F23BFC344D16C4DD');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF6A76ED395');
        $this->addSql('ALTER TABLE planning_recipe DROP FOREIGN KEY FK_BAA993583D865311');
        $this->addSql('ALTER TABLE planning_recipe DROP FOREIGN KEY FK_BAA9935859D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1359D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE13933FE08C');
    }
}
