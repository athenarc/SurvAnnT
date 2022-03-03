<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '4.x',
    'resources' => '../data/resources',
    'dataset' => '../data/dataset.json',
    'questions' => '../data/questions.json',
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
    'likert-5' => ['Strongly disagree', 'Disagree', 'Neither agree nor disagree', 'Agree', 'Strongly agree'],
    'likert-7' => ['Strongly disagree', 'Disagree', 'Somewhat Disagree', 'Neither agree nor disagree', 'Somewhat Agree', 'Agree', 'Strongly agree'],
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
        ],
    'about' =>
        [
            'Purpose' => 
                [
                    'link' => 'index.php?r=site%2Fabout&tab=',
                    'text' => 'This survey is taking place to help us gather insight about the readability of scientific abstracts. Since traditional readability metrics fail to distinguish effectively difficult from easy scientific texts, the goal is to establish a ground truth based on your assessments which will work as a basis for a tailor-made readability metric.',
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
        
];
