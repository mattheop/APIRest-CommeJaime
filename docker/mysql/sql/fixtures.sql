INSERT INTO users(id_user, username, password) VALUES (1, "John Doe", "$2y$10$mT97.3HQawCYDrAETOjLV.mgrjt/30Q/DJclf8CX5T55dNsk5kY8G");
INSERT INTO users(id_user, username, password, role) VALUES (2, "Jane Doe", "$2y$10$mT97.3HQawCYDrAETOjLV.mgrjt/30Q/DJclf8CX5T55dNsk5kY8G", "ROLE_MODERATOR");

INSERT INTO posts(title, content, id_user) VALUES ("My first post", "This is my first post", 1);
INSERT INTO posts(title, content, id_user) VALUES ("My second post", "This is my second post", 1);
INSERT INTO posts(title, content, id_user) VALUES ("Who i am", "This is Jane post !!", 2);

INSERT INTO liked(id_user, id_post) VALUES (1, 1);
INSERT INTO liked(id_user, id_post) VALUES (2, 1);
INSERT INTO liked(id_user, id_post) VALUES (2, 2);