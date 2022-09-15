<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '4.x',
    'resources' => '../data/resources',
    'dataset' => '../data/dataset.json',
    'dir-files' => '../data/files/',
    'questions' => '../data/questions/questions.json',
    'dir-python' => '../python/',
    'dir-articles' => '../data/resources/article/articles.json',
    'dir-texts' => '../data/resources/text/texts.json',
    'dir-images' => '../data/resources/image/',
    'dir-questionaires' => '../data/resources/questionaire/questionaire.json',
    'dir-badges' => '../data/badges/',
    'helpdesk-address' => 'survannt@athenarc.gr',
    'invitation-url' => 'https://survannt.athenarc.gr/index.php?r=user-management%2Fauth%2Fregistration&email=',
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
    'resources_allowlist' => ['text' => 'Text', 'article' => 'Article', 'image' => 'Image', 'questionaire' => 'No resources (Simple Questionaire)'],
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
            'General Settings' => 
                [
                    'link' => 'index.php?r=site%2Fsurvey-create&surveyid=',
                    'enabled' => 0,
                    'set' => '' //<i class="fas fa-circle-xmark"></i>
                ],
            'Resources' =>
                 [
                    'link' => 'index.php?r=resources%2Fresource-create&surveyid=',
                    'enabled' => 0,
                    'set' => '' //<i class="fas fa-circle-xmark"></i>
                ],
            'Questions' =>
                 [
                    'link' => 'index.php?r=questions%2Fquestions-create&surveyid=',
                    'enabled' => 0,
                    'set' => '' //<i class="fas fa-circle-xmark"></i>
                ],
            'Participants' =>
                 [
                    'link' => 'index.php?r=site%2Fparticipants-invite&surveyid=',
                    'enabled' => 0,
                    'set' => ''  //<i class="fas fa-circle-xmark"></i>
                ],
            'Badges' =>
                 [
                    'link' => 'index.php?r=badges%2Fbadges-create&surveyid=',
                    'enabled' => 0,
                    'set' => '' //<i class="fas fa-circle-xmark"></i>
                ],
            'Overview' => 
                [
                    'link' => 'index.php?r=site%2Fsurveys-view&surveyid=',
                    'enabled' => 0,
                    'set' => '' //<i class="fas fa-circle-xmark"></i>
                ]
        ],
    'about' =>
        [
            'What is SurvAnnT?' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'SurvAnnT is an open source  Web-based platform for the creation and management of both surveys and annotation projects. The platform provides the core functionalities of creating, editing and infering annotation campaigns and surveys.',
                    'enabled' => 1,

                ],
            'Architecture' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'SurvAnnT’s overall architecture, and the communication between its components is presented in the Figure below. 
                                Its core components are the campaign editor, which enables the creation and configuration of campaigns, the user manager,
                                which is responsible for the creation of new users and the management of their
                                roles and permissions, the expert finder, which offers recommendations for new
                                participants for campaigns according to their expertise, the campaign conductor,
                                that implements the required data entry procedures and the gamification features
                                for the campaigns, and the analyser, which is responsible for performing basic
                                analytics on the campaign results. All these components leverage SurvAnnT’s
                                DB, the underlying relational database to store and retrieve data. Finally, the
                                users interact with the platform through a Web-based Graphical User Interface.<br> <div class = "text-center"><img src ="images/survannt.png"></div>',
                    'enabled' => 1,
                ],
            'Citations' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'SurvAnnT citations',
                    'enabled' => 0,
                ],

            'Team' => 
                [ 
                    
                        [
                            'name' => 'Thanasis Vergoulis',
                            'title' => 'Post-doctoral researcher',
                            'url' => 'http://thanasis-vergoulis.com/',
                            'email' => 'vergoulis@athenarc.gr',
                            'image' => 'images/vergoulis.png',
                        ],
                    
                        [
                            'name' => 'Ilias Kanellos',
                            'title' => 'Research associate',
                            'url' => 'http://www.imsi.athenarc.gr/en/people/member/78',
                            'email' => 'kanellos@athenarc.gr',
                            'image' => 'images/kanellos.jpeg',
                        ],

                        [
                            'name' => 'Serafeim Chatzopoulos',
                            'title' => 'PhD candidate',
                            'url' => 'https://schatzopoulos.github.io/',
                            'email' => 'schatz@athenarc.gr',
                            'image' => 'images/serafeim.jpg',
                        ],

                        [
                            'name' => 'Theodore Dalamaggas',
                            'title' => 'Senior researcher',
                            'url' => 'http://www.imsi.athenarc.gr/en/people/member/4',
                            'email' => 'dalamagas@athenarc.gr',
                            'image' => 'images/dalamagas.jpg',
                        ],

                        [
                            'name' => 'Anargiros Tzerefos',
                            'title' => 'Developer',
                            'url' => 'https://www.imsi.athenarc.gr/en/people/member/100',
                            'email' => 'tzerefos@athenarc.gr',
                            'image' => 'images/tzerefos.jpg',
                        ]


                        
                ],
            'Contact us' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'Send us your feedback at: <br> &nbsp;&nbsp;<i class = "fa fa-envelope" style ="color:white;"> </i> survannt.helpdesk@gmail.gr <br> &nbsp;&nbsp;<i class = "fa fa-map-marker"> </i> Athena RC, Artemidos 6 & Epidavrou, Maroussi 15125, Greece <br>&nbsp;&nbsp;<a href="https://github.com/athenarc/SurvAnnT" target="_blank" class="fa-brands fa-github link-icon" style = "color:white;"> Github repository</a>',
                    'enabled' => 1,
                ]
        ],

        'Scoring-system' =>
            [
                'First-Badge-Earned' => 20,
                'Badge-Earned' => 20,
                'First-To-Earn-Badge' => 30,
                'Annotation' => 10,
                'Survey-Completion' => 50,
                'All-Badges' => 100, 
            ],

        
];
