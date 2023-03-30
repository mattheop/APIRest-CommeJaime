create database if not exists commejaime;
use commejaime;

create table if not exists users
(
    id_user  int                                       not null auto_increment,
    username varchar(50)                               not null,
    password varchar(100)                              not null,
    role     enum ('ROLE_PUBLISHER', 'ROLE_MODERATOR') not null default 'ROLE_PUBLISHER',

    primary key (id_user)
);

create table if not exists posts
(
    id_post    int          not null auto_increment,
    title      varchar(100) not null,
    content    text         not null,
    created_at datetime     not null default current_timestamp,
    id_user    int          not null,

    primary key (id_post),
    foreign key (id_user) references users (id_user),

    constraint title_start_uppercase_or_digit check (title regexp '^[A-Z0-9]')
);

create table if not exists liked
(
    id_liked int  not null auto_increment,
    id_post int  not null,
    id_user int  not null,
    is_up   bool not null default true,

    primary key (id_liked),
    constraint liked_unique unique (id_post, id_user),
    foreign key (id_post) references posts (id_post),
    foreign key (id_user) references users (id_user)
);

ALTER TABLE `liked` DROP FOREIGN KEY `liked_ibfk_1`; ALTER TABLE `liked` ADD CONSTRAINT `liked_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts`(`id_post`) ON DELETE CASCADE ON UPDATE CASCADE;