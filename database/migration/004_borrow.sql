-- Find books that are currently borrowed
SELECT
    br.borrow_id,

    -- Student Full Name
    CONCAT(
        s.student_first_name,
        ' ',
        s.student_last_name
    ) AS student_name,

    -- Books
    b.book_title,
    b.book_author,
    b.book_category,

    -- Borrow Date
    br.borrow_date

FROM borrow br

INNER JOIN students s
    ON br.student_id = s.student_id

INNER JOIN books b
    ON br.book_id = b.book_id

WHERE br.borrow_return_date IS NULL

ORDER BY br.borrow_date DESC;

-- View returned books

-- Find books that are currently borrowed
SELECT
    br.borrow_id,

    -- Student Full Name
    CONCAT(
        s.student_first_name,
        ' ',
        s.student_last_name
    ) AS student_name,

    -- Books
    b.book_title,
    b.book_author,
    b.book_category,

    -- Borrow Date
    br.borrow_date

FROM borrow br

INNER JOIN students s
    ON br.student_id = s.student_id

INNER JOIN books b
    ON br.book_id = b.book_id

WHERE br.borrow_return_date IS NOT NULL

ORDER BY br.borrow_date DESC;
