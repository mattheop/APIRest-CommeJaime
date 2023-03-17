INSERT INTO users(id_user, username, password) VALUES (1, "John Doe", "secret");
INSERT INTO users(id_user, username, password) VALUES (2, "Jane Doe", "secret");

INSERT INTO posts(title, content, id_user) VALUES ("My first post", "This is my first post", 1);
INSERT INTO posts(title, content, id_user) VALUES ("My second post", "This is my second post", 1);
INSERT INTO posts(title, content, id_user) VALUES ("Who i am", "This is Jane post !!", 2);