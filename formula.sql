CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner INT NOT NULL,
    title NVARCHAR(128),
    content BLOB
);