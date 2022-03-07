<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '4.x',
    'resources' => '../data/resources',
    'dataset' => '../data/dataset.json',
    'questions' => '../data/questions/questions.json',
    'dir-articles' => '../data/resources/article/articles.json',
    'dir-texts' => '../data/resources/text/texts.json',
    'dir-images' => '../data/resources/image/',
    'dir-questionaires' => '../data/resources/questionaire/questionaire.json',
    'images' => '../data/images/',
    'helpdesk-address' => 'survannt.helpdesk@gmail.com',
    'invitation-url' => 'http://localhost/SurveiFy/web/index.php?r=user-management%2Fauth%2Fregistration&email=',
    'title' => 'SurvAnnT',
    'fields' =>  
        [
            'Artificial Intelligence', 
            'Computer-Human Interface', 
            'Game Design', 
            'Networks', 
            'Computer Graphics', 
            'Information Security', 
            'Data Science',
            'Programming Languages',
            'Software Engineering',
            'Systems',
            'Theory'
        ],
    'resources_allowlist' => ['text' => 'Text', 'article' => 'Article', 'image' => 'Image', 'questionaire' => 'No resources (Single Questionaire)'],
    'likert-5' => 
        [ 
            1 => 'Strongly disagree', 
            2 => 'Disagree', 
            3 => 'Neither agree nor disagree', 
            4 => 'Agree', 
            5 => 'Strongly agree'
        ],
    'likert-7' => 
        [
            1 => 'Strongly disagree', 
            2 => 'Disagree', 
            3 => 'Somewhat Disagree', 
            4 => 'Neither agree nor disagree', 
            5 => 'Somewhat Agree', 
            6 => 'Agree', 
            7 => 'Strongly agree'
        ],
    'tabs' => 
        [
            'Campaign' => 
                [
                    'link' => 'index.php?r=site%2Fsurvey-create&surveyid=',
                    'enabled' => 0
                ],
            'Resources' =>
                 [
                    'link' => 'index.php?r=site%2Fresource-create&surveyid=',
                    'enabled' => 0
                ],
            'Questions' =>
                 [
                    'link' => 'index.php?r=site%2Fquestions-create&surveyid=',
                    'enabled' => 0 
                ],
            'Participants' =>
                 [
                    'link' => 'index.php?r=site%2Fparticipants-invite&surveyid=',
                    'enabled' => 0 
                ],
            'Badges' =>
                 [
                    'link' => 'index.php?r=site%2Fbadges-create&surveyid=',
                    'enabled' => 0 
                ],
            'Overview' => 
                [
                    'link' => 'index.php?r=site%2Fsurvey-overview&surveyid=',
                    'enabled' => 0 
                ]
        ],
    'about' =>
        [
            'Purpose' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'SurvAnnT offers functionalities to create, manage, and analyse survey and annotation campaingns.',
                    'enabled' => 1,

                ],
            'About page 2' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'We have selected a wide range of publication abstracts coming from various Journals, Conferences and Workshops through <a href="https://dblp.org">DBLP</a>. Since the dataset consisted of <b>227.296</b> abstracts, we discarded venues that were not directly related to computer science resulting in a pool of <b>77.299</b> abstracts derived from <b>120</b> venues. The following table presents the venues along with their respective number of abstracts.',
                    'enabled' => 1,
                ],
            'About page 3' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'The questions selected for the assessments reflect, in our opinion, many aspects of readability such as the overall <b>coherence</b> and <b>structure</b> of the text presented as well as the <b>style</b>, <b>syntax</b> and the <b>difficulty of terms explained</b>. In order to measure your provided feedback, we have selected a five point agreement <a href = "https://en.wikipedia.org/wiki/Likert_scale">Likert scale</a>, which is a widely used approach to scaling responses in survey research.',
                    'enabled' => 1,
                ]
        ],

        'Scoring-system' =>
            [
                'First-Badge' => 20,
                'Annotation' => 10,
                'Survey-Completion' => 50,
                'All-Badges' => 100, 
            ],

        
];
