#1 students table
CREATE TABLE IF NOT EXISTS students(
--Primary key forr tjhe students table
student_id INT PRIMARY KEY AUTO_INCREMENT,
 
--Student name
student_first_name VARCHAR(50) NOT NULL,
student_last_name VARCHAR(50) NOT NULL,

--Student course
student_course VARCHAR (50) NOT NULL,

--Student create at timestamp
student_created_at TIMESTAMP NOT NULL,
DEFAULT CURRENT_TIMESTAMP

)ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

--2 books table
CREATE TABLE IF NOT EXISTS books (
    bookd_id INT AUTO_INCREMENT PRIMARY KEY,

--book details
    book_title VARCHAR(50) NOT NULL,
    book_author VARCHAR(50) NOT NULL,
    book_category VARCHAR(50) NOT NULL,

--book created at timestamp
    book_created_at TIMESTAMP NOT NULL
    DEFAULT CURRENT_TIMESTAMP

)ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mn4_general_ci;

--#3 borrow table
CREATE TABLE IF NOT EXISTS borrow(
    --primary key for the borrow table
    borrow_id INT AUTO_INCREMENT PRIMARY KEY,

    --foreign key references
    student_id INT NOT NULL,
    book_id INT NOT NULL,

    --borrow timestamp not null by default
    borrow_date TIMESTAMP NOT NULL
    DEFAULT CURRENT_TIMESTAMP,

   --borrow return timestamp null by default
    borrow_return_date TIMESTAMP NULL
    DEFAULT NULL,

    --borrow tablle constraints and foreign keys
    CONSTRAINT fk_borrow_student
    FOREIGN KEY (student_id)
    REFERENCES students(student_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

    CONSTRAINT fk_borrow_book
    FOREIGN KEY (book_id)
    REFERENCES books(book_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT

)ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

-- insert statement #1: insert students
INSERT INTO students (student_first_name, student_last_name, student_course)
VALUES
('Juan', 'Dela Cruz', 'BSIT'),
('ARFRAEL', 'YBANEZ', 'BSCS'),
('ARMEL', 'BALDESCO', 'BSIT'),
('ARCHJAN', 'BALDESCO', 'BSBA'),
('MARIEL', 'BALDESCO', 'BSIT');

-- insert statement #2: insert books
INSERT INTO books (book_title, book_author, book_category)
VALUES
('365 Days', 'Fifty Shades', 'Noah Ark'),
('Alamat ni Juan', 'Alamat ng Gagamba', 'The Hobbit'),
('To kill a Mockingbird', 'The book Thief', 'The Alchemist');

-- insert statement #3: insert borrow   
INSERT INTO borrow (student_id, book_id
)VALUES
(1, 2)
(2, 1),
(3, 3);



