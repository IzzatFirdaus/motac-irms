<?php

// English - Contact Us translation lines for MOTAC IRMS (Enhanced)

return [
    'title'   => 'Contact Us', // Contact the Information Management Division (BPM), MOTAC
    'content' => <<< 'MD'
## Contact Information

For any inquiries, technical assistance related to ICT Equipment Loans, or feedback about the MOTAC Integrated Resource Management System, please contact us at the details below or use our internal Helpdesk system.

**Information Management Division (BPM)**
Ministry of Tourism, Arts and Culture (MOTAC)
Level 5, No. 2, Tower 1, Jalan P5/6, Precinct 5
62200 Putrajaya, Wilayah Persekutuan Putrajaya, MALAYSIA

**Phone**: +603-8000 8000
**Fax**: +603-8888 8624
**Email**: [bpm@motac.gov.my](mailto:bpm@motac.gov.my)

**Operating Hours**:
Monday - Friday: 8:00 AM - 5:00 PM
Saturday - Sunday: Closed

**Social Media**:
[Facebook](https://www.facebook.com/mymotac/) | [Instagram](https://www.instagram.com/mymotac/) | [X](https://x.com/mymotac) | [YouTube](https://www.youtube.com/user/mymotac) | [TikTok](https://www.tiktok.com/@mymotac)

**Location Map**:
No. 2, Tower 1, Jalan P5/6, Precinct 5, 62200 Putrajaya
Google Maps: [View Location](https://goo.gl/maps/3ZK5t8v2zv12)

> Please ensure you provide complete information to help us assist you effectively. We typically respond to inquiries within 1-2 business days.

---
MD
    ,
    'intro'         => 'For any inquiries, technical assistance related to ICT Equipment Loans, or feedback about the MOTAC Integrated Resource Management System, please contact us at the details below or visit our Helpdesk System.',
    'phone_title'   => 'Phone',
    'main_line'     => 'Main Line:',
    'fax'           => 'Fax:',
    'email_title'   => 'Email',
    'address_title' => 'Address',
    'division'      => 'Information Management Division (BPM)',
    'ministry'      => 'Ministry of Tourism, Arts and Culture (MOTAC)',
    // Updated address for BPM MOTAC
    'address_line1'   => 'Level 5, No. 2, Tower 1, Jalan P5/6, Precinct 5',
    'address_line2'   => '62200 Putrajaya, Wilayah Persekutuan Putrajaya',
    'address_country' => 'MALAYSIA',
    'note'            => 'Please ensure you provide complete information to help us assist you effectively. We typically respond to inquiries within 1-2 business days.',

    // Contact/MOTAC main ministry address (for ministry card if needed)
    'ministry_full_address_line1' => 'Ministry of Tourism, Arts and Culture (MOTAC)',
    'ministry_full_address_line2' => 'No. 2, Tower 1, Jalan P5/6, Precinct 5, 62200, Wilayah Persekutuan Putrajaya',

    // Contact info and location titles
    'contact_info_title' => 'Contact Information',
    'location_title'     => 'Our Location',

    // Operating hours
    'operating_hours_title' => 'Operating Hours',
    'weekdays'              => 'Monday - Friday',
    'weekdays_hours'        => '8:00 AM - 5:00 PM',
    'weekends'              => 'Saturday - Sunday',
    'weekends_hours'        => 'Closed',

    // Social media
    'social_media_title' => 'Follow Us',
    'social_media'       => [
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    ],

    // Contact form fields
    'form' => [
        'name'                => 'Full Name',
        'name_placeholder'    => 'Enter your full name',
        'email'               => 'Email Address',
        'email_placeholder'   => 'Enter your email address',
        'phone'               => 'Phone Number',
        'phone_placeholder'   => 'Enter your phone number (optional)',
        'inquiry_type'        => 'Type of Inquiry',
        'select_inquiry_type' => 'Please select inquiry type',
        'subject'             => 'Subject',
        'subject_placeholder' => 'Brief description of your inquiry',
        'message'             => 'Message',
        'message_placeholder' => 'Please provide detailed information about your inquiry...',
        'submit'              => 'Send Message',
        'reset'               => 'Clear Form',
        'consent'             => 'I agree to my data being processed as per the <a href="/privacy-policy" target="_blank">Personal Data Protection Act 2010</a>.',
    ],

    // Inquiry types
    'inquiry_types' => [
        'general'        => 'General Inquiry',
        'technical'      => 'Technical Support',
        'feedback'       => 'Feedback & Suggestions',
        'complaint'      => 'Complaint',
        'equipment_loan' => 'ICT Equipment Loan',
    ],

    // Validation messages
    'validation' => [
        'name_required'         => 'Please enter your full name.',
        'name_min'              => 'Name must be at least 2 characters long.',
        'email_required'        => 'Please enter your email address.',
        'email_invalid'         => 'Please enter a valid email address.',
        'phone_invalid'         => 'Please enter a valid phone number.',
        'subject_required'      => 'Please enter a subject for your inquiry.',
        'subject_min'           => 'Subject must be at least 5 characters long.',
        'message_required'      => 'Please enter your message.',
        'message_min'           => 'Message must be at least 10 characters long.',
        'inquiry_type_required' => 'Please select the type of inquiry.',
        'consent_required'      => 'You must agree to the Personal Data Protection Act 2010 to submit the form.',
    ],

    // Banner messages
    'success_title'    => 'Message Sent Successfully!',
    'success_message'  => 'Thank you for contacting us. We have received your message and will respond within 1-2 business days.',
    'error_title'      => 'Error',
    'submission_error' => 'Sorry, there was an error sending your message. Please try again later or contact us directly by phone.',
];
