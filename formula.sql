CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner INT NOT NULL,
    title NVARCHAR(128),
    blobId INT
);

CREATE TABLE note_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL,
    data LONGBLOB
);
