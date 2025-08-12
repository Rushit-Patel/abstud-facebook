<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Client Lead Welcome Email',
                'slug' => 'client-lead-welcome-email',
                'subject' => 'Thank You for Your Inquiry – {{ company_name }}',
                'html_template' => $this->getClientLeadWelcomeTemplate(),
                'text_template' => null,
                'variables' => [
                    'client_name',
                    'first_name',
                    'company_name',
                    'assigned_agent',
                    'assigned_agent_email',
                    'purpose',
                    'country',
                    'coaching_type',
                    'lead_id',
                    'current_date',
                    'app_url',
                    'contact_url'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Welcome email sent to new client leads when they are added to the system',
                'category' => 'Lead Management',
            ],
            [
                'name' => 'Assign New Task Team',
                'slug' => 'assign-new-task-team',
                'subject' => 'New Task Assigned: {{ task_title }}',
                'html_template' => $this->getAssignNewTaskTemplate(),
                'text_template' => null,
                'variables' => [
                    'assigned_agent',
                    'assigned_agent_email',
                    'task_title',
                    'task_description',
                    'task_priority',
                    'task_due_date',
                    'client_name',
                    'lead_id',
                    'current_date',
                    'dashboard_url',
                    'company_name'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Email sent to team members when a new task is assigned to them',
                'category' => 'Task Management',
            ],
            [
                'name' => 'Task Complete',
                'slug' => 'task-complete',
                'subject' => 'Task Completed: {{ task_title }}',
                'html_template' => $this->getTaskCompleteTemplate(),
                'text_template' => null,
                'variables' => [
                    'assigned_agent',
                    'task_title',
                    'task_description',
                    'task_completion_date',
                    'task_completion_notes',
                    'client_name',
                    'lead_id',
                    'current_date',
                    'dashboard_url',
                    'company_name'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Email sent when a task is marked as completed',
                'category' => 'Task Management',
            ],
            [
                'name' => 'Demo Lecture Schedule',
                'slug' => 'demo-lecture-schedule',
                'subject' => 'You\'re Invited: Free {demo_coaching} Demo Lecture on {demo_date} at {demo_time}',
                'html_template' => $this->getDemoLectureScheduleTemplate(),
                'text_template' => null,
                'variables' => [
                    'demo_coaching',
                    'demo_date',
                    'demo_time',
                    'client_name',
                    'branch_address',
                    'branch_map_link',
                    'company_name'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Demo booking email sent to client',
                'category' => null,
            ],
            [
                'name' => 'Close Inquiry',
                'slug' => 'close-inquiry',
                'subject' => 'Your Inquiry Status – {company_name}',
                'html_template' => $this->getCloseInquiryTemplate(),
                'text_template' => null,
                'variables' => [
                    'client_name',
                    'service',
                    'branch_address',
                    'branch_map_link',
                    'branch_contact',
                    'company_website',
                    'company_mail',
                    'company_name'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Inquiry Closed email sent to client',
                'category' => null,
            ],
            [
                'name' => 'Register In Coaching',
                'slug' => 'register-in-coaching',
                'subject' => 'Welcome to {coaching} {coaching_batch} – Let\'s Begin Your Journey!',
                'html_template' => $this->getRegisterInCoachingTemplate(),
                'text_template' => null,
                'variables' => [
                    'coaching',
                    'coaching_batch',
                    'client_name',
                    'company_name',
                    'coaching_length',
                    'branch_address',
                    'branch_map_link',
                    'branch_contact',
                    'company_website',
                    'company_mail'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'Inquiry registered in coaching email sent to client',
                'category' => null,
            ],
            [
                'name' => 'Exam Date Booking',
                'slug' => 'exam-date-booking',
                'subject' => '{exam_name} Exam Date Booked – Confirmation & Important Details',
                'html_template' => $this->getExamDateBookingTemplate(),
                'text_template' => null,
                'variables' => [
                    'exam_name',
                    'client_name',
                    'company_name',
                    'exam_date',
                    'exam_center',
                    'exam_type',
                    'exam_way',
                    'exam_passport_no',
                    'exam_mode',
                    'branch_address',
                    'branch_map_link',
                    'branch_contact',
                    'company_website',
                    'company_mail'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'exam date booking email sent to client',
                'category' => null,
            ],
            [
                'name' => 'Result Received',
                'slug' => 'result-received',
                'subject' => 'Let\'s Plan Your Next Step After {exam_name} – Book Your FREE Country Counseling Session',
                'html_template' => $this->getResultReceivedTemplate(),
                'text_template' => null,
                'variables' => [
                    'exam_name',
                    'client_name',
                    'company_name',
                    'countries',
                    'branch_address',
                    'branch_map_link',
                    'branch_contact',
                    'company_website',
                    'company_mail'
                ],
                'is_system' => true,
                'is_active' => true,
                'description' => 'result received email sent to client',
                'category' => null,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    /**
     * Get Client Lead Welcome HTML Template
     */
    private function getClientLeadWelcomeTemplate(): string
    {
        return '<p>Dear&nbsp;{client_name},</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Thank you for contacting </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{ company_name }</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">. We\'ve successfully received your inquiry regarding </span><strong>{ purpose }</strong>, <span style="background-color: transparent; color: rgb(0, 0, 0);">and our team will reach out to you shortly with complete guidance and assistance.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Whether you\'re planning for </span><strong>{ purpose }</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> or looking for </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{ country },</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> we are here to guide you every step of the way.</span></p><p>\r\n</p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">For faster assistance, feel free to contact us directly or visit our branch.</span></p><p><br></p><p>\r\n</p><p>Best regards,</p><p>\r\n</p><p>The {company_name} Team</p>';
    }

    /**
     * Get Assign New Task HTML Template
     */
    private function getAssignNewTaskTemplate(): string
    {
        return '<h2>Hello <strong>{assigned_agent}</strong>,</h2>\n<p><br></p>\n<p>A new task has been assigned to you. Please review the details below and take necessary action.</p>\n<p></p>\n<h3><strong>Task Details</strong>:</h3>\n<p class="ql-align-justify"><br></p>\n    <ul>\n        <li class="ql-align-justify">Task Title: {task_title}</li>\n        <li class="ql-align-justify">Description: {task_description}</li>\n        <li class="ql-align-justify">Priority: {task_priority}</li>\n        <li class="ql-align-justify">Due Date: {task_due_date}</li>\n        <li class="ql-align-justify">Client: {client_name}</li>\n        <li class="ql-align-justify">Lead ID: {lead_id}</li>\n        <li class="ql-align-justify">Assigned Date: {current_date}</li>\n    </ul>\n    <p><br></p>\n    <p><strong>Action Required</strong>:</p>\n    <p><br></p>\n    <ol>\n        <li>Review the task details carefully</li>\n        <li>Plan your approach to complete the task</li>\n        <li>Update task status as you progress</li>\n        <li>Complete the task before the due date</li>\n    </ol>\n    <p>If you have any questions about this task or need clarification, please contact your supervisor or the task creator.</p>\n    <p>Thank you for your attention to this matter.</p>\n    <p>Best regards,</p>\n    <p>{company_name} Team</p>';
    }

    /**
     * Get Task Complete HTML Template
     */
    private function getTaskCompleteTemplate(): string
    {
        return '<h4><strong>Great Work</strong>, {assigned_agent}!</h4><h4>\n</h4><p>We\'re pleased to inform you that the following task has been marked as completed. Thank you for your dedication and excellent work.</p><p>\n</p><h3>Completed Task Summary:</h3><p>\t- Task Title: {task_title}</p><p>\t-  Description: {task_description}</p><p>    - Client: {client_name}</p><p>    - Lead ID: {lead_id}</p><p>    - Completion Date: {task_completion_date}</p><p>    - Completed By: {assigned_agent}\n</p><p>\n</p><h3><strong>What\'s Next:</strong></h3><p>\n</p><p>Your consistent effort and dedication contribute significantly to our team\'s success and client satisfaction. Keep up the excellent work!</p><p>\n</p><p>Best regards,</p><p>{company_name} Management Team</p>';
    }

    /**
     * Get Demo Lecture Schedule HTML Template
     */
    private function getDemoLectureScheduleTemplate(): string
    {
        return '<p><span style="background-color: transparent; color: rgb(0, 0, 0);">Dear </span>{client_name}<span style="background-color: transparent; color: rgb(0, 0, 0);">,</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Thank you for your interest in </span><strong>{demo_coaching}</strong> <strong style="background-color: transparent; color: rgb(0, 0, 0);">Coaching</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> at </span><strong style="color: rgb(0, 0, 0);">{company_name}</strong><strong style="background-color: transparent; color: rgb(0, 0, 0);">.</strong></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We\'re pleased to invite you for a </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Free Demo Lecture</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">, where you\'ll get a firsthand experience of our expert-led teaching methodology and strategies to boost your </span>{demo_coaching}<span style="background-color: transparent; color: rgb(0, 0, 0);"> score.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">🗓️ </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Demo Date: </strong>{demo_date}</p><p>⏰ <strong style="background-color: transparent; color: rgb(0, 0, 0);">Time:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{demo_time}</p><p>📍 <strong style="background-color: transparent; color: rgb(0, 0, 0);">Location:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> {company_name}.</span></p><p>{branch_address}</p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">📌 Click here for Directions </span>{branch_map_link}</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">What You\'ll Learn:</span></p><p><br></p><ul><li class="ql-align-justify"><span style="background-color: transparent;">{demo_coaching} Module Overview</span></li><li class="ql-align-justify"><span style="background-color: transparent;">Scoring Techniques</span></li><li class="ql-align-justify"><span style="background-color: transparent;">Tips from Experienced Trainers</span></li><li class="ql-align-justify"><span style="background-color: transparent;">Q&amp;A Session</span></li></ul><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Seats are limited – make sure to reach on time!</span></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">For any questions, feel free to contact us at 📞 </span>{branch_contact}<span style="background-color: transparent; color: rgb(0, 0, 0);"> or reply to this email.</span></p><p><br></p><p>Best regards,</p><p><br></p><p>The {company_name} Team</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Let me know if you want templates for </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">PTE, TOEFL, or Duolingo</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> demo lectures as well.</span></p><p><br></p>';
    }

    /**
     * Get Close Inquiry HTML Template
     */
    private function getCloseInquiryTemplate(): string
    {
        return '<p><span style="background-color: transparent; color: rgb(0, 0, 0);">Dear {client_name},</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Thank you for connecting with </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{company_name}.</strong></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We appreciate your interest in our services for {service}. As per our recent communication, we understand that your inquiry is now being closed due to the some reason.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Please know that we completely respect your decision, and you are always welcome to reach out to us anytime in the future.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">At </span>{company_name}<span style="background-color: transparent; color: rgb(0, 0, 0);">, we\'re always here to help with:</span></p><p><br></p><ul><li><span style="background-color: transparent;">Student, Visitor, Dependent Visa</span></li><li><span style="background-color: transparent;">IELTS, PTE, TOEFL &amp; Duolingo Coaching</span></li><li><span style="background-color: transparent;">Expert Career Guidance for Global Education</span></li></ul><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">📍 </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Branch Address:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{company_name}<span style="background-color: transparent; color: rgb(0, 0, 0);">,&nbsp;</span>{branch_address}</p><p><br></p><p> 📌 <strong>Location</strong><strong style="color: rgb(0, 0, 0); background-color: transparent;">: </strong>{branch_map_link}</p><p> 📞 <strong style="background-color: transparent; color: rgb(0, 0, 0);">Contact:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{branch_contact}</p><p> 🌐 <strong>Website:</strong> {company_website}</p><p><span style="background-color: transparent; color: rgb(0, 0, 0);"> ✉️ </span><strong>Email:</strong> {company_mail}</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We wish you great success ahead! 🌟</span></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Warm regards,</span></p><p>The {company_name} Team</p>';
    }

    /**
     * Get Register In Coaching HTML Template
     */
    private function getRegisterInCoachingTemplate(): string
    {
        return '<p><span style="background-color: transparent; color: rgb(0, 0, 0);">Dear {client_name},</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We\'re thrilled to welcome you to the </span>{coaching}<strong style="background-color: transparent; color: rgb(0, 0, 0);"> Coaching Program</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> at </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{company_name}</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">.</span></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">You\'ve been successfully enrolled in the </span><strong>{coaching_batch}</strong> <span style="background-color: transparent; color: rgb(0, 0, 0);">with a </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{coaching_length}</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">. Your learning journey officially begins now, and we\'re here to support you every step of the way.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">To achieve your desired band score, we encourage you to:</span></p><p><br></p><ul><li><span style="background-color: transparent;">📘 </span><strong style="background-color: transparent;">Attend classes regularly</strong></li><li><span style="background-color: transparent;">📝 Take scheduled </span><strong style="background-color: transparent;">Mock Tests</strong><span style="background-color: transparent;"> to assess your progress</span></li><li>🎤 Book your <strong style="background-color: transparent;">One-on-One Speaking Practice</strong><span style="background-color: transparent;"> sessions with our expert trainers</span></li></ul><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Our commitment is to help you not just prepare - but excel.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">📍 </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Branch Address:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{company_name}<span style="background-color: transparent; color: rgb(0, 0, 0);">,&nbsp;</span>{branch_address}</p><p><br></p><p> 📌 <strong>Location</strong><strong style="background-color: transparent; color: rgb(0, 0, 0);">: </strong>{branch_map_link}</p><p> 📞 <strong style="background-color: transparent; color: rgb(0, 0, 0);">Contact:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{branch_contact}</p><p> 🌐 <strong>Website:</strong> {company_website}</p><p><span style="background-color: transparent; color: rgb(0, 0, 0);"> ✉️ </span><strong>Email:</strong> {company_mail}</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Let\'s work together to make your global dreams come true!</span></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Best wishes,</span></p><p>The {company_name} Team</p>';
    }

    /**
     * Get Exam Date Booking HTML Template
     */
    private function getExamDateBookingTemplate(): string
    {
        return '<p><span style="background-color: transparent; color: rgb(0, 0, 0);">Dear </span><span style="color: rgb(0, 0, 0);">{client_name}</span><span style="background-color: transparent; color: rgb(0, 0, 0);">,</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We are pleased to inform you that your </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{exam_name} exam has been successfully booked</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> through </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{ company_name }</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> Please find the booking details below:</span></p><h3><br></h3><h3><strong style="background-color: transparent; color: rgb(0, 0, 0);">✅ Booking Confirmation Details:</strong></h3><p><br></p><ul><li><span style="background-color: transparent; color: rgb(0, 0, 0);">🗓️ </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Exam Date:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span><span style="color: rgb(0, 0, 0);">{exam_date}</span></li><li><span style="background-color: transparent;">📍 </span><strong style="background-color: transparent;">Test Center:</strong><span style="background-color: transparent;"> </span><span style="color: rgb(0, 0, 0);">{exam_center}</span></li><li>📝 <strong style="background-color: transparent;">Module:</strong><span style="background-color: transparent;"> </span><span style="color: rgb(0, 0, 0);">{exam_type}</span></li><li>💻 <strong style="background-color: transparent;">Test Format:</strong><span style="background-color: transparent;"> </span><span style="color: rgb(0, 0, 0);">{exam_way}</span></li><li>🆔 <strong style="background-color: transparent;">ID Used:</strong><span style="background-color: transparent;"> Passport – </span><span style="color: rgb(0, 0, 0);">{exam_passport_no}</span></li></ul><p><br></p><h3><strong style="background-color: transparent; color: rgb(0, 0, 0);">📌 Important Instructions:</strong></h3><p><br></p><ul><li><span style="background-color: transparent;">Bring your </span><strong style="background-color: transparent;">original passport</strong><span style="background-color: transparent;"> on the day of the exam.</span></li><li>Arrive at the test center <strong style="background-color: transparent;">at least 30 minutes before</strong><span style="background-color: transparent;"> your scheduled reporting time.</span></li><li><span style="background-color: transparent; color: rgb(0, 0, 0);">You will receive an email from </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{exam_name} official test partners ({exam_mode})</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> with your candidate details and test center address. Please check your inbox (and spam folder).</span></li></ul><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">If you have any questions or need help with test day preparation, feel free to contact us.</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">📍 </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">Branch Address:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{company_name}<span style="background-color: transparent; color: rgb(0, 0, 0);">,&nbsp;</span>{branch_address}</p><p><br></p><p> 📌 <strong>Location</strong><strong style="background-color: transparent; color: rgb(0, 0, 0);">: </strong>{branch_map_link}</p><p> 📞 <strong style="background-color: transparent; color: rgb(0, 0, 0);">Contact:</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> </span>{branch_contact}</p><p> 🌐 <strong>Website:</strong> {company_website}</p><p><span style="background-color: transparent; color: rgb(0, 0, 0);"> ✉️ </span><strong>Email:</strong> {company_mail}</p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Wishing you the best of luck for your test!</span></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Warm regards,</span></p><p>The {company_name} Team</p>';
    }

    /**
     * Get Result Received HTML Template
     */
    private function getResultReceivedTemplate(): string
    {
        return '<p><span style="background-color: transparent; color: rgb(0, 0, 0);">Dear </span><span style="color: rgb(0, 0, 0);">{client_name}</span><span style="background-color: transparent; color: rgb(0, 0, 0);">,</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Congratulations on completing your </span><strong>{exam_name}</strong><strong style="background-color: transparent; color: rgb(0, 0, 0);"> exam</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> – whether you\'re happy with your result or planning to improve further, now is the perfect time to explore your </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">study abroad opportunities</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">!</span></p><p><br></p><p><span style="background-color: transparent; color: rgb(0, 0, 0);">We\'re inviting you for a </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">free, personalized country counseling session</strong><span style="background-color: transparent; color: rgb(0, 0, 0);"> at </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{company_name}</strong><span style="background-color: transparent; color: rgb(0, 0, 0);">, where our experts will help you:</span></p><p><br></p><ul><li><span style="background-color: transparent;">📍 Match your {exam_name} score with the best universities</span></li><li><span style="background-color: transparent;">🌎 Explore suitable options in </span><strong style="background-color: transparent; color: rgb(0, 0, 0);">{countries}</strong><span style="background-color: transparent;"> etc</span></li><li><span style="background-color: transparent;">📝 Get clarity on admission timelines, visa process, and documentation</span></li><li><span style="background-color: transparent;">🎓 Design the right academic and immigration path based on your goals</span></li></ul><p><br></p><h3><strong style="background-color: transparent; color: rgb(0, 0, 0);">📅 Sessions Available This Week</strong></h3><p><span style="background-color: transparent; color: rgb(0, 0, 0);">Reserve your slot by calling us or visiting our office.</span></p><p><br></p><p><span style="color: rgb(0, 0, 0); background-color: transparent;">📍 </span><strong style="color: rgb(0, 0, 0); background-color: transparent;">Branch Address:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> </span>{company_name}<span style="color: rgb(0, 0, 0); background-color: transparent;">,&nbsp;</span>{branch_address}</p><p><br></p><p> 📌 <strong>Location</strong><strong style="color: rgb(0, 0, 0); background-color: transparent;">: </strong>{branch_map_link}</p><p> 📞 <strong style="color: rgb(0, 0, 0); background-color: transparent;">Contact:</strong><span style="color: rgb(0, 0, 0); background-color: transparent;"> </span>{branch_contact}</p><p> 🌐 <strong>Website:</strong> {company_website}</p><p><span style="color: rgb(0, 0, 0); background-color: transparent;"> ✉️ </span><strong>Email:</strong> {company_mail}</p><p><br></p><p><span style="background-color: transparent;">Warm regards,</span></p><p>The {company_name} Team</p>';
    }
}
