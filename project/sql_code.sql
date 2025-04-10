CREATE TABLE patient_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    checkup_date DATE,
    hospital_name VARCHAR(100),
    doctor_id VARCHAR(20),
    doctor_consulted VARCHAR(100),
    reason_for_visit VARCHAR(255),
    diagnosis VARCHAR(255),
    lab_tests VARCHAR(255),
    test_results VARCHAR(255),
    medications_prescribed VARCHAR(255),
    using_or_not VARCHAR(10)
);

INSERT INTO patient_records (patient_id, name, checkup_date, hospital_name, doctor_id, doctor_consulted, reason_for_visit, diagnosis, lab_tests, test_results, medications_prescribed, using_or_not) VALUES
('P011', 'Janshi', '2024-09-07', 'RK Hospital', 'D101', 'Dr. Rama Krishna', 'Thyroid Symptoms', 'Thyroid Disorder', 'Blood Test', 'Confirmed Thyroid Issue', 'Tab. Thyronorm 50 mcg', 'No'),
('P011', 'Janshi', '2025-03-10', 'RK Hospital', 'D101', 'Dr. Rama Krishna', 'Ear Pain', NULL, NULL, NULL, 'Ear Drops: Candibiotic Plus', 'Yes'),
('P047', 'Jahnavy', '2022-01-02', 'SVS Hospital', 'D102', 'Dr. Berapa', 'Stomach Pain', 'Stomach Infection', 'Scanning', 'Infection Detected', 'Tab. Oflox-OZ (Ofloxacin 200mg + Ornidazole 500mg)', 'No'),
('P013', 'Shireesha', '2025-03-30', 'Shamil Hospital', 'D103', 'Dr. Shamil', 'Weakness due to summer', 'Dehydration/Weakness', NULL, NULL, 'IV Fluids: Dextrose 5%, Tab. Livogen-Z, Cap. Becosules', 'Yes'),
('P011', 'Janshi', '2023-06-24', 'Power Pills Hospital', 'D104', 'Dr. Sharath Chandra', 'Migraine', 'Migraine', NULL, NULL, 'Homoeopathy Medicine: Belladonna 30C', 'Yes'),
('P078', 'Vaishnavi', '2025-04-05', 'Datatraya Hospital', 'D105', 'Dr. Datta', 'Shin pain', 'Possible Shin Fracture', NULL, NULL, 'Tab. Shelcal 500, Tab. Calpol 650', 'Yes'),
('P021', 'Padmama', '2025-03-02', 'RK Hospital', 'D101', 'Dr. Rama Krishna', 'Knee Pain', 'Knee Strain', NULL, NULL, 'Tab. Zerodol-SP, Gel: Volini', 'Yes'),
('P089', 'Padmama', '2025-01-15', 'RK Hospital', 'D101', 'Dr. Rama Krishna', 'Fever', 'Viral Fever & Weakness', NULL, NULL, 'Tab. Dolo-650, IV Saline (0.9% NS)', NULL),
('P056', 'Anil', '2024-02-06', 'Sridhar Reddy Multispeciality Hospital', 'D106', 'Dr. Sridhar Reddy', 'Chicken Pox', 'Chicken Pox', NULL, NULL, 'Tab. Acyclovir 400 mg, Calamine Lotion, Paracetamol', 'No'),
('P0100', 'Aruna', '2024-09-20', 'Govt-hospital(JDCL)', 'D111', 'Dr.padmavathi', 'vomting', NULL, NULL, NULL, 'two glucose, paracetamal, palonosetron, granisetron', 'No'),
('P047', 'Jahnavy', '2023-07-27', 'MAA Hospital', 'D107', 'Dr. Keerthi', 'Recurring Stomach Infection', 'Stomach Infection', NULL, NULL, 'Tab. Rifaximin 400 mg, Tab. Pantoprazole 40 mg', 'No'),
('P029', 'Shobha', '2023-09-20', 'Govt-hospital(MBNR)', 'D109', 'Dr. Jyothi', 'High BP', 'Hypertension Stage I', 'BP Monitoring', '160/100 mmHg', 'Tab. Amlodipine 5mg, Tab. Telmisartan 40mg', 'Yes'),
('P056', 'Anil', '2025-03-15', 'Sridhar Reddy Multispeciality Hospital', 'D110', 'Dr. Sridhar Reddy', 'Eye Redness', 'Eye irritation', NULL, NULL, 'Eye Drops: Moxifloxacin, Tab. Cetirizine', 'No'),
('P0111', 'Venkatesh', '2022-11-06', 'Govt-hospital(MBNR)', 'D118', 'Dr. Swetha Reddy', 'Joint Pain', 'Arthritis', 'X-Ray', 'Joint Wear Noted', 'Tab. Etoricoxib 90mg, Physiotherapy Suggested', 'No'),
('P094', 'Maheshwari', '2025-03-25', 'Govt-hospital(MBNR)', 'D129', 'Dr. Laxmi Prasad', 'Appendix Pain', 'ppendicitis', 'Ultrasound', 'Inflammation Detected', 'Appendectomy Done, Tab. Cefixime 200mg + Painkillers', 'Yes');
