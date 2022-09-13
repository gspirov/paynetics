<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220911182319 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
                create type STATUS_ENUM as enum ('new', 'pending', 'failed', 'done');
SQL
        );

        $this->addSql(
            <<<SQL
                create function update_updated_at()
                returns trigger as $$
                begin
                    new.updated_at = now();
                    return new;
                end;
                $$ language 'plpgsql';
SQL
        );

        $this->addSql(
            <<<SQL
                create table project (
                    id serial primary key,
                    title varchar(155) not null, 
                    description varchar(255) not null, 
                    status STATUS_ENUM not null default 'new', 
                    start_date timestamp(0) without time zone not null, 
                    end_date timestamp(0) without time zone not null, 
                    client varchar(155) default null, 
                    company varchar(155) default null,
                    created_at timestamp(0) without time zone not null default now(), 
                    updated_at timestamp(0) without time zone not null default now(), 
                    deleted_at timestamp(0) without time zone default null, 
                    check ( end_date > start_date ),
                    check ( num_nonnulls (client, company) = 1 )
                );
SQL
        );

        $this->addSql(
            <<<SQL
                create trigger update_project_updated_at
                before update
                on project
                for each row
                execute procedure update_updated_at();
SQL
        );

        $this->addSql(
            <<<SQL
                create table task (
                    id serial primary key,
                    "name" varchar(155) not null,
                    project_id integer not null,
                    status STATUS_ENUM not null default 'new', 
                    start_date timestamp(0) without time zone not null, 
                    end_date timestamp(0) without time zone not null,
                    created_at timestamp(0) without time zone not null default now(), 
                    updated_at timestamp(0) without time zone not null default now(), 
                    deleted_at timestamp(0) without time zone default null,
                    check ( end_date > start_date ),
                    constraint fk_task_project 
                    foreign key (project_id) 
                    references project (id)
                );
SQL
        );

        $this->addSql(
            <<<SQL
                create trigger update_task_updated_at
                before update
                on task
                for each row
                execute procedure update_updated_at();
SQL
        );

        $this->addSql(
            <<<SQL
                create function ensure_task_time_frame_project_validity()
                returns trigger as $$
                begin
                    if (not exists(
                        select *
                        from project p
                        where p.id = new.project_id 
                        and p.start_date <= new.start_date 
                        and p.end_date >= new.end_date
                    )) then
                        raise exception 'Task time frame does not fit in project''s one.' using errcode = 'P0001';
end if;
                    return new;
                end;
                $$ language 'plpgsql';
SQL
        );

        $this->addSql(
            <<<SQL
                create trigger task_time_frame_validity
                before insert or update 
                on task
                for each row  
                execute procedure ensure_task_time_frame_project_validity();
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
                drop table if exists task;            
SQL
        );

        $this->addSql(
            <<<SQL
                drop table if exists project;
SQL
        );

        $this->addSql(
            <<<SQL
                drop function if exists ensure_task_time_frame_project_validity;
SQL
        );

        $this->addSql(
            <<<SQL
                drop function if exists update_updated_at;
SQL
        );

        $this->addSql(
            <<<SQL
                drop type if exists STATUS_ENUM;
SQL
        );
    }
}
