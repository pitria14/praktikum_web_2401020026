USE praktikum_web_2401020026;

-- INSERT program studi
INSERT INTO program_studi (nama_prodi)
VALUES
('Teknik Informatika'),
('Teknik Elektro');

-- INSERT mahasiswa
INSERT INTO mahasiswa (nim, nama, email, usia, program_studi_id)
VALUES
('2401020026', 'Pitria', 'pitria@gmail.com', 20, 1),
('2401020025', 'Nurul Syafika', 'nurulsyafika@gmail.com', 21, 1),
('2401020007', 'Alfa Julyana', 'alfajulyana@gmail.com', 19, 2),
('2401020027', 'Nurdina Firdaus', 'nurdinafirdaus@gmail.com', 18, 2);

-- UPDATE
UPDATE mahasiswa
SET email = 'pitria.pit@gmail.com'
WHERE nim = '2401020026';

-- DELETE
DELETE FROM mahasiswa
WHERE nim = '2401020099';

-- SELECT JOIN
SELECT
    mahasiswa.nim,
    mahasiswa.nama,
    mahasiswa.email,
    mahasiswa.usia,
    program_studi.nama_prodi
FROM mahasiswa
JOIN program_studi
    ON mahasiswa.program_studi_id = program_studi.id;