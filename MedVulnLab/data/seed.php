<?php
/**
 * Data Seeding
 * 
 * Simulates healthcare data using PHP arrays.
 * In a real application, this would be a database.
 */

require_once __DIR__ . '/../config/security.php';

/**
 * Get all users (simulated database)
 */
function get_all_users() {
    return [
        // Doctors
        'doc001' => [
            'id' => 'doc001',
            'username' => 'dr_smith',
            'password' => 'password123', // Insecure for lab purposes
            'role' => 'doctor',
            'name' => 'Dr. Sarah Smith',
            'specialization' => 'Cardiology',
            'assigned_patients' => ['pat001', 'pat002', 'pat003']
        ],
        'doc002' => [
            'id' => 'doc002',
            'username' => 'dr_jones',
            'password' => 'password123',
            'role' => 'doctor',
            'name' => 'Dr. Michael Jones',
            'specialization' => 'Neurology',
            'assigned_patients' => ['pat004', 'pat005']
        ],
        
        // Patients
        'pat001' => [
            'id' => 'pat001',
            'username' => 'patient_alice',
            'password' => 'password123',
            'role' => 'patient',
            'name' => 'Alice Johnson',
            'dob' => '1985-03-15',
            'assigned_doctor' => 'doc001'
        ],
        'pat002' => [
            'id' => 'pat002',
            'username' => 'patient_bob',
            'password' => 'password123',
            'role' => 'patient',
            'name' => 'Bob Williams',
            'dob' => '1978-07-22',
            'assigned_doctor' => 'doc001'
        ],
        'pat003' => [
            'id' => 'pat003',
            'username' => 'patient_carol',
            'password' => 'password123',
            'role' => 'patient',
            'name' => 'Carol Davis',
            'dob' => '1992-11-08',
            'assigned_doctor' => 'doc001'
        ],
        'pat004' => [
            'id' => 'pat004',
            'username' => 'patient_david',
            'password' => 'password123',
            'role' => 'patient',
            'name' => 'David Brown',
            'dob' => '1980-05-30',
            'assigned_doctor' => 'doc002'
        ],
        'pat005' => [
            'id' => 'pat005',
            'username' => 'patient_eve',
            'password' => 'password123',
            'role' => 'patient',
            'name' => 'Eve Martinez',
            'dob' => '1995-09-12',
            'assigned_doctor' => 'doc002'
        ],
        
        // Lab Technician
        'lab001' => [
            'id' => 'lab001',
            'username' => 'lab_tech',
            'password' => 'password123',
            'role' => 'lab_technician',
            'name' => 'Lab Technician'
        ],
        
        // Pharmacist
        'pharm001' => [
            'id' => 'pharm001',
            'username' => 'pharmacist',
            'password' => 'password123',
            'role' => 'pharmacist',
            'name' => 'Pharmacist'
        ],
        
        // Billing Officer
        'bill001' => [
            'id' => 'bill001',
            'username' => 'billing_officer',
            'password' => 'password123',
            'role' => 'billing_officer',
            'name' => 'Billing Officer'
        ],
        
        // Administrator
        'admin001' => [
            'id' => 'admin001',
            'username' => 'admin',
            'password' => 'password123',
            'role' => 'administrator',
            'name' => 'System Administrator'
        ]
    ];
}

/**
 * Get user by username
 */
function get_user_by_username($username) {
    $users = get_all_users();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by ID
 */
function get_user_by_id($user_id) {
    $users = get_all_users();
    return $users[$user_id] ?? null;
}

/**
 * Get patient records (simulated database)
 */
function get_patient_records() {
    return [
        'pat001' => [
            'patient_id' => 'pat001',
            'name' => 'Alice Johnson',
            'dob' => '1985-03-15',
            'medical_history' => 'Hypertension, Type 2 Diabetes',
            'current_medications' => ['Metformin 500mg', 'Lisinopril 10mg'],
            'allergies' => ['Penicillin'],
            'last_visit' => '2024-01-15',
            'assigned_doctor' => 'doc001'
        ],
        'pat002' => [
            'patient_id' => 'pat002',
            'name' => 'Bob Williams',
            'dob' => '1978-07-22',
            'medical_history' => 'High Cholesterol',
            'current_medications' => ['Atorvastatin 20mg'],
            'allergies' => [],
            'last_visit' => '2024-01-20',
            'assigned_doctor' => 'doc001'
        ],
        'pat003' => [
            'patient_id' => 'pat003',
            'name' => 'Carol Davis',
            'dob' => '1992-11-08',
            'medical_history' => 'Asthma',
            'current_medications' => ['Albuterol Inhaler'],
            'allergies' => ['Dust Mites'],
            'last_visit' => '2024-01-18',
            'assigned_doctor' => 'doc001'
        ],
        'pat004' => [
            'patient_id' => 'pat004',
            'name' => 'David Brown',
            'dob' => '1980-05-30',
            'medical_history' => 'Migraines',
            'current_medications' => ['Sumatriptan 50mg'],
            'allergies' => [],
            'last_visit' => '2024-01-22',
            'assigned_doctor' => 'doc002'
        ],
        'pat005' => [
            'patient_id' => 'pat005',
            'name' => 'Eve Martinez',
            'dob' => '1995-09-12',
            'medical_history' => 'Epilepsy',
            'current_medications' => ['Levetiracetam 500mg'],
            'allergies' => [],
            'last_visit' => '2024-01-25',
            'assigned_doctor' => 'doc002'
        ]
    ];
}

/**
 * Get appointments (simulated database)
 */
function get_appointments() {
    return [
        'apt001' => [
            'appointment_id' => 'apt001',
            'patient_id' => 'pat001',
            'doctor_id' => 'doc001',
            'date' => '2024-02-10',
            'time' => '10:00',
            'status' => 'scheduled',
            'reason' => 'Follow-up for diabetes management'
        ],
        'apt002' => [
            'appointment_id' => 'apt002',
            'patient_id' => 'pat002',
            'doctor_id' => 'doc001',
            'date' => '2024-02-12',
            'time' => '14:00',
            'status' => 'scheduled',
            'reason' => 'Cholesterol check'
        ]
    ];
}

/**
 * Get lab reports (simulated database)
 */
function get_lab_reports() {
    return [
        'lab001' => [
            'report_id' => 'lab001',
            'patient_id' => 'pat001',
            'doctor_id' => 'doc001',
            'test_type' => 'Blood Glucose',
            'result' => '95 mg/dL',
            'status' => 'completed',
            'created_at' => '2024-01-15 10:30:00',
            'workflow_state' => 'diagnosis' // diagnosis -> lab_report -> billing_approval -> completed
        ],
        'lab002' => [
            'report_id' => 'lab002',
            'patient_id' => 'pat002',
            'doctor_id' => 'doc001',
            'test_type' => 'Lipid Panel',
            'result' => 'Total Cholesterol: 180 mg/dL',
            'status' => 'completed',
            'created_at' => '2024-01-20 11:00:00',
            'workflow_state' => 'lab_report'
        ]
    ];
}

/**
 * Get prescriptions (simulated database)
 */
function get_prescriptions() {
    return [
        'rx001' => [
            'prescription_id' => 'rx001',
            'patient_id' => 'pat001',
            'doctor_id' => 'doc001',
            'medication' => 'Metformin 500mg',
            'dosage' => 'Twice daily',
            'status' => 'pending',
            'created_at' => '2024-01-15 10:45:00'
        ]
    ];
}

/**
 * Get billing records (simulated database)
 */
function get_billing_records() {
    return [
        'bill001' => [
            'billing_id' => 'bill001',
            'patient_id' => 'pat001',
            'appointment_id' => 'apt001',
            'amount' => 150.00,
            'status' => 'pending_approval',
            'created_at' => '2024-01-15 11:00:00',
            'approval_token' => 'token_' . md5('bill001' . 'pat001' . '2024-01-15')
        ]
    ];
}
