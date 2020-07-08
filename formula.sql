CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username NVARCHAR(64) NOT NULL,
    password NVARCHAR(128) NOT NULL,
    question NVARCHAR(128) NOT NULL,
    answer NVARCHAR(128) NOT NULL
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner INT NOT NULL,
    title NVARCHAR(128),
    date DATE,
    blobId INT
);

CREATE TABLE note_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL,
    data LONGBLOB
);
