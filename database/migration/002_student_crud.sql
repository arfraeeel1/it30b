-- student SQL #1 : select 1 student
SELECT * FROM students;

-- students SQL #2 : select students in asc order by id;
SELECT * FROM students
ORDER BY student_id ASC;

-- students SQL #3 : select students in asc order by id;
SELECT * FROM students
ORDER BY student_id DESC;

-- students SQL #4 : select students in asc order by last_name;
SELECT * FROM students
ORDER BY student_last_name ASC;

-- students SQL #5 : select students in asc order by last_name;
SELECT * FROM students
ORDER BY student_last_name DESC;

-- students SQL #6 : select students in asc order by first_name;
SELECT * FROM students
ORDER BY student_first_name ASC;

-- students SQL #7 : select students in asc order by last_name;
SELECT * FROM students
ORDER BY student_first_name DESC;

-- You can modify displayed columns by selecting
-- specific columns after SELECT command
-- STUDENT sql#8 display all students first_name and last_name

SELECT student_first_name,
 student_last_name
 FROM students
 ORDER BY student_first_name ASC;

 -- student SQL#9 LIMIT 1 - You can change the limit to any number
 SELECT student_first_name,
 student_last_name

 FROM students
 ORDER BY student_first_name ASC
 LIMIT 1;

 -- student SQL#10- select a student based on id
 SELECT student_first_name,
        student_last_name

 FROM students
 WHERE student_id = 1
 LIMIT 1;

 -- student SQL#11- update student name a student based on id

 UPDATE students
 SET student_first_name = 'Arfrael',
 student_last_name = 'Baldesco'
 WHERE student_id = 2;