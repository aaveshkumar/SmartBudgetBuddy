<?php
require_once __DIR__ . '/../../config/database.php';

class JobSeekerProfile {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // Main Profile Methods
    public function getProfile($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM jobseeker_profiles WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createProfile($data) {
        $stmt = $this->db->prepare("
            INSERT INTO jobseeker_profiles (
                user_id, headline, summary, phone, address, city, state, country,
                postal_code, date_of_birth, gender, linkedin_url, github_url,
                portfolio_url, total_experience_years, current_salary, expected_salary,
                notice_period, availability, willing_to_relocate
            ) VALUES (
                :user_id, :headline, :summary, :phone, :address, :city, :state, :country,
                :postal_code, :date_of_birth, :gender, :linkedin_url, :github_url,
                :portfolio_url, :total_experience_years, :current_salary, :expected_salary,
                :notice_period, :availability, :willing_to_relocate
            )
        ");
        
        return $stmt->execute($data);
    }
    
    public function updateProfile($userId, $data) {
        $data['user_id'] = $userId;
        
        $stmt = $this->db->prepare("
            UPDATE jobseeker_profiles SET
                headline = :headline,
                summary = :summary,
                phone = :phone,
                address = :address,
                city = :city,
                state = :state,
                country = :country,
                postal_code = :postal_code,
                date_of_birth = :date_of_birth,
                gender = :gender,
                linkedin_url = :linkedin_url,
                github_url = :github_url,
                portfolio_url = :portfolio_url,
                total_experience_years = :total_experience_years,
                current_salary = :current_salary,
                expected_salary = :expected_salary,
                notice_period = :notice_period,
                availability = :availability,
                willing_to_relocate = :willing_to_relocate
            WHERE user_id = :user_id
        ");
        
        return $stmt->execute($data);
    }
    
    public function updateProfilePicture($userId, $filename) {
        $stmt = $this->db->prepare("
            UPDATE jobseeker_profiles SET profile_picture = :filename
            WHERE user_id = :user_id
        ");
        return $stmt->execute(['user_id' => $userId, 'filename' => $filename]);
    }
    
    public function updateResume($userId, $filename) {
        $stmt = $this->db->prepare("
            UPDATE jobseeker_profiles SET resume_file = :filename
            WHERE user_id = :user_id
        ");
        return $stmt->execute(['user_id' => $userId, 'filename' => $filename]);
    }
    
    // Work Experience Methods
    public function getWorkExperiences($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM work_experiences
            WHERE user_id = :user_id
            ORDER BY is_current DESC, start_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addWorkExperience($data) {
        $stmt = $this->db->prepare("
            INSERT INTO work_experiences (
                user_id, job_title, company_name, location, employment_type,
                start_date, end_date, is_current, description, achievements
            ) VALUES (
                :user_id, :job_title, :company_name, :location, :employment_type,
                :start_date, :end_date, :is_current, :description, :achievements
            )
        ");
        return $stmt->execute($data);
    }
    
    public function updateWorkExperience($id, $userId, $data) {
        $data['id'] = $id;
        $data['user_id'] = $userId;
        
        $stmt = $this->db->prepare("
            UPDATE work_experiences SET
                job_title = :job_title,
                company_name = :company_name,
                location = :location,
                employment_type = :employment_type,
                start_date = :start_date,
                end_date = :end_date,
                is_current = :is_current,
                description = :description,
                achievements = :achievements
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute($data);
    }
    
    public function deleteWorkExperience($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM work_experiences WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Education Methods
    public function getEducation($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM education
            WHERE user_id = :user_id
            ORDER BY end_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addEducation($data) {
        $stmt = $this->db->prepare("
            INSERT INTO education (
                user_id, degree, field_of_study, institution, location,
                start_date, end_date, grade, description
            ) VALUES (
                :user_id, :degree, :field_of_study, :institution, :location,
                :start_date, :end_date, :grade, :description
            )
        ");
        return $stmt->execute($data);
    }
    
    public function deleteEducation($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM education WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Skills Methods
    public function getSkills($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM skills
            WHERE user_id = :user_id
            ORDER BY proficiency DESC, skill_name ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addSkill($data) {
        $stmt = $this->db->prepare("
            INSERT INTO skills (user_id, skill_name, proficiency, years_of_experience)
            VALUES (:user_id, :skill_name, :proficiency, :years_of_experience)
        ");
        return $stmt->execute($data);
    }
    
    public function deleteSkill($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM skills WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Certifications Methods
    public function getCertifications($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM certifications
            WHERE user_id = :user_id
            ORDER BY issue_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addCertification($data) {
        $stmt = $this->db->prepare("
            INSERT INTO certifications (
                user_id, certification_name, issuing_organization, issue_date,
                expiry_date, credential_id, credential_url, description
            ) VALUES (
                :user_id, :certification_name, :issuing_organization, :issue_date,
                :expiry_date, :credential_id, :credential_url, :description
            )
        ");
        return $stmt->execute($data);
    }
    
    public function deleteCertification($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM certifications WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Languages Methods
    public function getLanguages($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM languages
            WHERE user_id = :user_id
            ORDER BY proficiency DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addLanguage($data) {
        $stmt = $this->db->prepare("
            INSERT INTO languages (user_id, language_name, proficiency)
            VALUES (:user_id, :language_name, :proficiency)
        ");
        return $stmt->execute($data);
    }
    
    public function deleteLanguage($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM languages WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Projects Methods
    public function getProjects($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM projects
            WHERE user_id = :user_id
            ORDER BY is_ongoing DESC, end_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addProject($data) {
        $stmt = $this->db->prepare("
            INSERT INTO projects (
                user_id, project_name, project_url, description,
                technologies_used, start_date, end_date, is_ongoing
            ) VALUES (
                :user_id, :project_name, :project_url, :description,
                :technologies_used, :start_date, :end_date, :is_ongoing
            )
        ");
        return $stmt->execute($data);
    }
    
    public function deleteProject($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM projects WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
    
    // Admin & Employer - Get all candidates with filters
    public function searchCandidates($filters = []) {
        $sql = "SELECT 
                    u.id, u.name, u.email,
                    jp.headline, jp.city, jp.state, jp.total_experience_years,
                    jp.expected_salary, jp.availability, jp.profile_picture,
                    jp.resume_file, jp.willing_to_relocate,
                    GROUP_CONCAT(DISTINCT s.skill_name) as skills
                FROM users u
                LEFT JOIN jobseeker_profiles jp ON u.id = jp.user_id
                LEFT JOIN skills s ON u.id = s.user_id
                WHERE u.type = 'jobseeker'";
        
        $params = [];
        
        if (!empty($filters['city'])) {
            $sql .= " AND jp.city LIKE :city";
            $params['city'] = '%' . $filters['city'] . '%';
        }
        
        if (!empty($filters['state'])) {
            $sql .= " AND jp.state LIKE :state";
            $params['state'] = '%' . $filters['state'] . '%';
        }
        
        if (!empty($filters['min_experience'])) {
            $sql .= " AND jp.total_experience_years >= :min_exp";
            $params['min_exp'] = $filters['min_experience'];
        }
        
        if (!empty($filters['max_experience'])) {
            $sql .= " AND jp.total_experience_years <= :max_exp";
            $params['max_exp'] = $filters['max_experience'];
        }
        
        if (!empty($filters['availability'])) {
            $sql .= " AND jp.availability = :availability";
            $params['availability'] = $filters['availability'];
        }
        
        if (!empty($filters['skill'])) {
            $sql .= " AND s.skill_name LIKE :skill";
            $params['skill'] = '%' . $filters['skill'] . '%';
        }
        
        $sql .= " GROUP BY u.id ORDER BY jp.updated_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get complete profile with all sections
    public function getCompleteProfile($userId) {
        return [
            'profile' => $this->getProfile($userId),
            'work_experiences' => $this->getWorkExperiences($userId),
            'education' => $this->getEducation($userId),
            'skills' => $this->getSkills($userId),
            'certifications' => $this->getCertifications($userId),
            'languages' => $this->getLanguages($userId),
            'projects' => $this->getProjects($userId)
        ];
    }
}
