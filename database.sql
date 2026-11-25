DROP DATABASE IF EXISTS blood_donation_db;
CREATE DATABASE blood_donation_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood_donation_db;

CREATE TABLE donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    department VARCHAR(100) NOT NULL,
    campus VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    last_donation_date DATE DEFAULT NULL,
    is_available TINYINT(1) DEFAULT 1,
    total_donations INT DEFAULT 0,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE blood_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units_needed INT NOT NULL DEFAULT 1,
    hospital_name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    urgency ENUM('Critical','Urgent','Normal') DEFAULT 'Urgent',
    status ENUM('Open','Fulfilled','Closed') DEFAULT 'Open',
    notes TEXT,
    needed_by DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES donors(donor_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    request_id INT,
    donation_date DATE NOT NULL,
    location VARCHAR(150),
    notes TEXT,
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id) ON DELETE CASCADE,
    FOREIGN KEY (request_id) REFERENCES blood_requests(request_id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE responses (
    response_id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    donor_id INT NOT NULL,
    message TEXT,
    status ENUM('Pending','Accepted','Declined') DEFAULT 'Pending',
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES blood_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_blood_group ON donors(blood_group);
CREATE INDEX idx_campus ON donors(campus);
CREATE INDEX idx_last_donation ON donors(last_donation_date);
CREATE INDEX idx_request_status ON blood_requests(status);
CREATE INDEX idx_request_blood ON blood_requests(blood_group);

CREATE OR REPLACE VIEW eligible_donors AS
SELECT 
    donor_id, student_id, full_name, blood_group, phone, email,
    department, campus, last_donation_date, total_donations,
    CASE 
        WHEN last_donation_date IS NULL THEN 'Never Donated'
        WHEN DATEDIFF(CURDATE(), last_donation_date) >= 90 THEN 'Eligible'
        ELSE 'Not Eligible'
    END AS eligibility_status,
    CASE 
        WHEN last_donation_date IS NULL THEN NULL
        ELSE DATEDIFF(CURDATE(), last_donation_date)
    END AS days_since_donation
FROM donors
WHERE is_available = 1;

INSERT INTO donors (student_id, full_name, email, password, phone, blood_group, department, campus, address, last_donation_date, total_donations) VALUES
('CSE-2021-001', 'Rahim Ahmed', 'rahim@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01711111111', 'O+', 'Computer Science', 'Dhaka University', 'Dhanmondi, Dhaka', '2025-08-15', 5),
('EEE-2021-045', 'Karim Hossain', 'karim@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01722222222', 'A+', 'Electrical Engineering', 'BUET', 'Mirpur, Dhaka', '2025-11-20', 3),
('BBA-2022-112', 'Fatima Begum', 'fatima@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01733333333', 'B+', 'Business Administration', 'NSU', 'Bashundhara, Dhaka', NULL, 0),
('CSE-2020-078', 'Sadia Islam', 'sadia@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01744444444', 'AB+', 'Computer Science', 'Dhaka University', 'Mohammadpur, Dhaka', '2025-06-10', 7),
('ME-2021-023', 'Tanvir Hasan', 'tanvir@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01755555555', 'O-', 'Mechanical Engineering', 'BUET', 'Uttara, Dhaka', '2026-02-01', 2),
('PHA-2022-090', 'Nusrat Jahan', 'nusrat@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01766666666', 'A-', 'Pharmacy', 'NSU', 'Gulshan, Dhaka', '2025-09-22', 4),
('CSE-2023-156', 'Imran Khan', 'imran@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01777777777', 'B-', 'Computer Science', 'BRAC University', 'Mohakhali, Dhaka', NULL, 0),
('CIV-2021-067', 'Mehedi Hasan', 'mehedi@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01788888888', 'O+', 'Civil Engineering', 'BUET', 'Tejgaon, Dhaka', '2025-12-05', 6),
('LAW-2022-034', 'Ayesha Rahman', 'ayesha@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01799999999', 'AB-', 'Law', 'Dhaka University', 'Old Dhaka', '2025-07-18', 3),
('CSE-2021-201', 'Rifat Ahmed', 'rifat@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01700000000', 'A+', 'Computer Science', 'BRAC University', 'Banani, Dhaka', '2026-01-15', 8),
('EEE-2022-088', 'Sumaiya Akter', 'sumaiya@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01811111111', 'O+', 'Electrical Engineering', 'AIUB', 'Kuril, Dhaka', '2025-10-30', 4),
('CSE-2020-145', 'Arif Mahmud', 'arif@university.edu.bd', '$2y$10$abcdefghijklmnopqrstuv', '01822222222', 'B+', 'Computer Science', 'Dhaka University', 'Shahbag, Dhaka', NULL, 0);

INSERT INTO blood_requests (requester_id, patient_name, blood_group, units_needed, hospital_name, location, contact_phone, urgency, status, notes, needed_by) VALUES
(1, 'Mr. Abdul Karim', 'O+', 2, 'Dhaka Medical College Hospital', 'Bakshibazar, Dhaka', '01711111111', 'Critical', 'Open', 'Accident victim, urgent surgery needed', '2026-05-07'),
(3, 'Mrs. Salma Begum', 'B+', 1, 'Square Hospital', 'Panthapath, Dhaka', '01733333333', 'Urgent', 'Open', 'Cancer patient, regular transfusion', '2026-05-08'),
(5, 'Baby Rayhan', 'O-', 1, 'Apollo Hospital', 'Bashundhara, Dhaka', '01755555555', 'Critical', 'Open', 'Premature baby, very urgent', '2026-05-06'),
(2, 'Mr. Faruk Ahmed', 'A+', 3, 'United Hospital', 'Gulshan, Dhaka', '01722222222', 'Urgent', 'Fulfilled', 'Heart surgery patient', '2026-04-25'),
(7, 'Ms. Tahmina', 'B-', 2, 'Labaid Hospital', 'Dhanmondi, Dhaka', '01777777777', 'Normal', 'Open', 'Scheduled surgery next week', '2026-05-12');

INSERT INTO donations (donor_id, request_id, donation_date, location, notes) VALUES
(1, NULL, '2025-08-15', 'Red Crescent Society', 'Voluntary donation'),
(2, 4, '2025-11-20', 'United Hospital', 'Emergency response'),
(4, NULL, '2025-06-10', 'BSMMU Blood Bank', 'Regular donor'),
(5, NULL, '2026-02-01', 'BUET Medical Center', 'Campus donation drive'),
(6, NULL, '2025-09-22', 'Quantum Foundation', 'Voluntary donation'),
(8, NULL, '2025-12-05', 'Sandhani Blood Bank', 'Emergency call response'),
(10, 4, '2026-01-15', 'United Hospital', 'Heart surgery support'),
(11, NULL, '2025-10-30', 'Red Crescent Society', 'Voluntary donation');

INSERT INTO responses (request_id, donor_id, message, status) VALUES
(1, 1, 'I am available, on my way to hospital', 'Accepted'),
(1, 8, 'Can reach in 30 minutes', 'Pending'),
(2, 11, 'Available tomorrow morning', 'Pending'),
(3, 5, 'Reaching the hospital now', 'Accepted'),
(5, 7, 'Can come on the scheduled date', 'Pending');
