**DESIGN OF A DATABASE-DRIVEN PERSONALIZED LEARNING RESOURCE
RECOMMENDATION SYSTEM FOR COMPETENCY-BASED EDUCATION AT ST. LAWRENCE
UNIVERSITY**

**CASE STUDY: ST. LAWRENCE UNIVERSITY UGANDA**

**BY**

**TREVOR KIYOMBO KAZZORA**

**A RESEARCH PROPOSAL SUBMITTED TO THE DEPARTMENT OF INFORMATICS AND
ENGINEERING, FACULTY OF SCIENCE AND TECHNOLOGY IN PARTIAL FULFILMENT OF
THE REQUIREMENTS FOR THE AWARD OF THE BACHELOR\'S DEGREE IN INFORMATION
TECHNOLOGY OF ST. LAWRENCE UNIVERSITY**

# 

# 

# 

# ABSTRACT

This research focuses on the design of a database-driven personalized
learning resource recommendation system for competency-based education
at St. Lawrence University Uganda. The study aims to address the
challenges students face in accessing learning resources tailored to
specific competencies in the context of the university\'s transition to
a Competency-Based Curriculum (CBC). Using a case study design, data
will be collected through interviews and questionnaires from lecturers
and students in the Faculty of Science and Technology. The proposed
system will be developed using PHP and MySQL, providing a rule-based
recommendation approach that links resources to courses and
competencies. The system is expected to enhance the efficiency of
resource access, support the implementation of competency-based
education, and improve student learning outcomes. The study recommends
that the university adopt such a system to facilitate the successful
implementation of CBC.

**Keywords**: Competency-Based Education, Personalized Learning,
Recommendation System, Database-Driven System, St. Lawrence University

# CHAPTER ONE: INTRODUCTION

## 1.1 Introduction

This chapter introduces the research by providing the background to the
study, the problem statement, the objectives, research questions, scope,
significance, and a summary. The focus is on the integration of
technology into competency-based education, specifically at St. Lawrence
University Uganda. The chapter sets the stage for understanding why a
personalized learning resource recommendation system is necessary and
how it aligns with current educational reforms.

The higher education sector in Uganda comprises public and private
universities, with over 50 institutions registered by the National
Council for Higher Education (NCHE, 2022). The government, through
Vision 2040 and the National Development Plan III, has prioritised
education as a key driver of economic transformation (Oketch, 2020). The
information technology industry in Uganda is expanding rapidly,
demanding graduates with practical skills in software development,
networking, database management, and systems analysis (Kintu, 2023). St.
Lawrence University Uganda, established in 2005, has embraced
Competency-Based Education (CBE) in its curricula, particularly in the
Faculty of Science and Technology.

## 1.2 Background to the Study

Competency-Based Education (CBE) is defined by Gervais (2016) as a
system where students progress by demonstrating mastery of specific
competencies, rather than by completing a fixed amount of time in class.
This model has gained traction globally because it aligns more closely
with workforce needs (Le, Wolfe and Steinberg, 2018). In Uganda, the
shift towards CBE is evident in the revised curricula of several
universities, including St. Lawrence University (Nannyonjo, 2021).

The role of Information and Communication Technology (ICT) in enabling
CBE cannot be overstated. Digital platforms allow for the storage,
retrieval, and dissemination of learning resources, but most existing
systems, such as Moodle and Google Classroom, are designed for general
course management rather than granular competency-based recommendations
(Mayisela, 2019). According to Kintu (2023), students often receive a
single list of resources for an entire course, forcing them to sift
through irrelevant materials to find what they need for a specific topic
or skill.

At St. Lawrence University, the implementation of CBE has highlighted
this gap. Preliminary discussions with lecturers in the Faculty of
Science and Technology revealed that students frequently request
resources for narrow topics, and lecturers spend considerable time
redirecting them (Kaguhangire-Barifaijo, 2022). A structured system that
links resources directly to competencies could save time and enhance
learning outcomes.

The idea of personalised learning recommendation systems is not new.
Dascalu et al. (2021) categorise such systems in technology-enhanced
learning as rule-based, content-based, collaborative, or hybrid.
However, many advanced systems rely on artificial intelligence and
machine learning, which require expertise and infrastructure not readily
available in many Ugandan universities (Obeid et al., 2024). This
research therefore proposes a simpler, database-driven approach that
leverages existing skills in web development and database management.
Such a system can be built using PHP and MySQL, technologies familiar to
the researcher (Nixon, 2021; Duckett, 2022).

## 1.3 Problem Statement

St. Lawrence University is in the process of transitioning to a
Competency-Based Curriculum (CBC), as guided by the National Council for
Higher Education (NCHE). While this transition is underway, there exists
a critical gap in the way students access learning resources. Currently,
learning materials are distributed either as hard copies in the library
or uploaded in bulk to general course portals. This one-size-fits-all
approach does not align with the competency-based philosophy, which
requires personalized learning pathways tailored to individual
competencies.

Students are expected to locate resources relevant to the specific
competency they are studying on their own, leading to several problems:

- Time wastage: Students spend hours searching for materials directly
  related to a given competency (Nabizadeh et al., 2020).

- Frustration and disengagement: When students cannot find appropriate
  resources, they may become discouraged and lose motivation (Burns,
  2021).

- Inconsistent quality: Resources found by students may not be vetted by
  lecturers, leading to misinformation.

- Misalignment with CBC goals: The core principle of CBC is that
  learning should be personalized and competency-based. Without a system
  that supports this personalization, the full benefits of CBC cannot be
  realized.

If this gap is not addressed, the successful implementation of CBC at
St. Lawrence University may be hindered, and the intended outcomes of
competency-based education may not be fully achieved. There is therefore
a pressing need to design a system that can recommend learning resources
to students based on the specific competencies they are pursuing, in
preparation for the full implementation of CBC.

## 1.4 Objectives

## 1.4.1 General Objective

The main objective of this research is to design a prototype of a
database-driven web system for St. Lawrence University that can
recommend personalised learning resources to students based on their
course and the specific competency they are studying.

## 1.4.2 Specific Objectives (SMART)

1.  To study and analyse the current methods of accessing learning
    resources for students at St. Lawrence University and identify the
    challenges they face.

2.  To design a database model for storing courses, competencies,
    learning resources, and user (student/lecturer) information.

3.  To develop a functional web-based prototype using PHP and MySQL that
    allows students to view resources filtered by course and competency.

4.  To test and validate the functionality of the developed prototype
    with a small group of users to ensure it meets the defined
    requirements.

These objectives are Specific, Measurable, Achievable, Realistic, and
Time-bound (SMART). Each objective corresponds directly to a research
question and will be accomplished within the six-month timeframe of the
study.

## 1.5 Research Questions

Specific Objective Corresponding Research Question

1.  To study current methods and challenges What are the current
    challenges students at St. Lawrence University face when trying to
    find learning materials for their courses?

2.  To design a database model How can courses, competencies, and
    resources be structured and stored in a relational database to
    facilitate personalised recommendations?

3.  To develop a web-based prototype How can a web-based interface be
    designed to allow students to easily select a competency and receive
    relevant resource recommendations?

4.  To test and validate the prototype Does the developed prototype
    successfully recommend resources that are relevant to the selected
    competency?

## 1.6 Scope of the Study

## 1.6.1 Subject Scope

The study focuses on the design and development of a prototype for
recommending learning resources. The system is limited to text-based
resources (links, documents, video URLs) and does not include multimedia
streaming or automatic resource tagging. The recommendation logic is
rule-based: students will select a course and a competency, and the
system will retrieve all resources associated with that competency.
Advanced features such as user tracking, adaptive algorithms, or mobile
applications are beyond the scope of this research.

## 1.6.2 Geographical Scope

The research will be conducted at St. Lawrence University Uganda,
specifically within the Faculty of Science and Technology. The case
study will involve students and lecturers from this faculty to ensure
relevance and accessibility.

## 

## 1.7 Significance of the Study

This study is significant for several stakeholders:

- Students: They will gain a tool that saves time and provides
  high-quality, lecturer-approved resources tailored to their specific
  learning needs, thereby enhancing their mastery of competencies
  (Nabizadeh et al., 2020).

- Lecturers: They will have a structured platform to organise and share
  resources, reducing the need to repeatedly answer the same queries
  from students.

- The University: The system will support the successful implementation
  of the CBC by providing a practical technological solution that aligns
  with competency-based pedagogy (NCHE, 2022).

- Researchers and Developers: The study will contribute a working
  example of a database-driven recommendation system that can be adapted
  by other institutions with similar constraints (Obeid et al., 2024).

- The Researcher: The project will serve as a practical application of
  skills in web development, database design, and systems analysis,
  contributing to professional growth.

**1.8 Chapter Summary**

This chapter has provided the foundation for the research. It began with
an overview of the shift towards competency-based education and the role
of technology. The problem was identified as the lack of a personalised
resource recommendation system at St. Lawrence University. The
objectives were defined to address this gap, and corresponding research
questions were formulated. The scope clarified what the study will and
will not cover, and the significance highlighted the benefits to various
stakeholders. The next chapter reviews existing literature related to
CBE, recommendation systems, and database applications in education.

# CHAPTER TWO: LITERATURE REVIEW

## 2.1 Introduction

This chapter reviews existing literature related to the key concepts of
this research: Competency-Based Education, the role of technology in
education, personalised recommendation systems, and database
applications. The purpose is to understand what is already known, to
identify theoretical foundations, and to pinpoint the gap that this
research will fill. The chapter is organized to support the specific
objectives of the study.

**2.2 Theoretical Framework**

This study is grounded in two main theories:

1.  **Constructivist Learning Theory**

As articulated by Piaget (1954) and Vygotsky (1978), constructivism
emphasises that learners actively construct knowledge by interacting
with relevant resources. Personalised recommendations align with
constructivist principles by providing resources that match the
learner\'s immediate needs, allowing them to build understanding at
their own pace (Nabizadeh et al., 2020).

2.  **Technology Acceptance Model (TAM)**

Developed by Davis (1989), TAM posits that perceived usefulness and
perceived ease of use influence technology adoption. The proposed system
will be designed to be easy to use and clearly useful to both students
and lecturers, increasing the likelihood of successful adoption (Kintu,
2023).

## 2.3 Conceptual Framework

The conceptual framework for this study illustrates the relationship
between the key variables. The independent variables are the competency
structure (courses and competencies) and the resource repository
(learning materials tagged with competencies). The dependent variable is
the personalised resource recommendation that students receive. The
system acts as an intermediary, processing the student\'s selection to
generate a filtered list (Dascalu et al., 2021).

![Figure 1: Conceptual
Framework](media/image1.png){alt="C:\\Users\\DELL\\Downloads\\graph (14).png"
width="6.493055555555555in" height="3.1875in"}

Source: Adapted from Dascalu et al. (2021)

**2.4 Objective 1: Current Methods and Challenges in Accessing Learning
Resources**

To address the first objective of this study, this section reviews
literature on the current methods students use to access learning
resources and the challenges they face.

According to Nabizadeh et al. (2020), students in higher education
institutions often struggle to find relevant learning materials due to
the unstructured nature of resource distribution. In many universities,
learning materials are either uploaded in bulk to general course portals
or made available as hard copies in libraries, forcing students to
manually search for resources that match their specific learning needs.

Kintu (2023) notes that students frequently receive a single list of
resources for an entire course, which requires them to sift through
irrelevant materials to find what they need for a specific topic or
skill. This one-size-fits-all approach leads to significant time
wastage, with students spending hours searching for materials directly
related to a given competency (Nabizadeh et al., 2020).

The challenges are further compounded by the adoption of
Competency-Based Education (CBE). Burns (2021) argues that CBE requires
personalised learning pathways, yet most institutions lack the
technological infrastructure to support individualised resource
recommendations. Kaguhangire-Barifaijo (2022) adds that in Ugandan
universities, the shift towards CBE has highlighted the gap between
curriculum goals and the availability of structured, competency-linked
resources.

Mayisela (2019) observes that existing Learning Management Systems (LMS)
such as Moodle and Google Classroom are designed for general course
management rather than granular competency-based recommendations. This
means that even when digital platforms are available, students are not
able to access resources that are explicitly linked to the specific
competencies they are studying.

The problem is not limited to Uganda. Research by Obeid et al. (2024)
indicates that students globally face similar challenges, with many
reporting frustration and disengagement when they cannot find
appropriate resources. This dissatisfaction can lead to reduced
motivation and lower academic performance.

In summary, the literature reveals a consistent pattern: students in CBE
environments need personalised resource recommendations, but existing
systems do not provide this functionality. This gap forms the basis for
the current study, which aims to address the challenges students face in
accessing learning resources.

## Objective 2: Database Design for Competency-Based Systems

To address the second objective of this study, this section reviews
literature on database design principles and their application in
competency-based learning systems.

A database-driven system for personalised learning requires a
well-structured relational database model. According to Silberschatz,
Korth and Sudarshan (2020), a relational database organises data into
tables that are linked through relationships, allowing for efficient
queries and data retrieval.

For a competency-based learning resource recommendation system, the
database must store information about courses, competencies, learning
resources, and users (students and lecturers). The relationship between
these entities is crucial: a course has many competencies, a competency
can have many resources, a student is enrolled in many courses, and a
lecturer teaches many courses (Connolly and Begg, 2015).

Beaulieu (2020) explains that SQL (Structured Query Language) can be
used to retrieve all resources linked to a given competency with a
simple SELECT statement, making it possible to provide personalised
recommendations based on the student\'s selected competency.

The proposed system adopts a rule-based recommendation approach, which
Dascalu et al. (2021) describe as suitable for educational contexts
where transparency and simplicity are valued. This approach uses
predefined rules (e.g., if a student studies competency X, recommend
resources tagged with X) and can be implemented using a well-designed
relational database.

In summary, the literature supports the use of a relational database
model (MySQL) to store and retrieve competency-linked resources,
enabling personalised recommendations through rule-based logic.

**2.5.1 General Database Design Principles**

Database design is the process of structuring data so that it can be
stored, retrieved, and managed efficiently. According to Silberschatz,
Korth and Sudarshan (2020), a well-designed database should ensure data
integrity, minimize redundancy, and support fast query performance. The
relational model, which organizes data into tables with relationships,
is the most widely used approach for structured data management.

**2.5.2 Existing Database Designs in Educational Systems**

Several educational systems use relational databases to manage learning
resources. For example, Moodle, a popular Learning Management System
(LMS), uses a MySQL database with tables for courses, users, activities,
and resources. Similarly, Blackboard uses a structured database to
organize course content and student data. These systems demonstrate that
relational databases are effective for managing educational content.
However, most of these systems do not explicitly link resources to
specific competencies, which is the gap this research aims to address.

## Web-Based Prototype Development (PHP/MySQL)

To address the third objective of this study, this section reviews
literature on web-based prototype development using PHP and MySQL.

System development for educational applications often follows the System
Development Life Cycle (SDLC), which includes requirements analysis,
design, implementation, testing, and deployment (Connolly and Begg,
2015). For this study, a prototype will be developed using PHP and
MySQL, technologies that are widely accessible and maintainable by local
IT staff (Nixon, 2021).

PHP is a server-side scripting language commonly used for web
development, while MySQL is an open-source relational database
management system (Duckett, 2022). Together, they provide a robust
platform for building database-driven web applications.

Research by Obeid et al. (2024) indicates that rule-based recommendation
systems can be effectively implemented using PHP and MySQL, particularly
in contexts where AI-based approaches are not feasible due to resource
constraints. This is consistent with the findings of Kintu (2023), who
emphasises the importance of using accessible technologies in Ugandan
higher institutions.

The prototype will include a user interface for students to select a
course and competency, a database to store competency-linked resources,
and a recommendation engine that retrieves relevant resources based on
the student\'s selection (Nabizadeh et al., 2020).

In summary, the literature confirms that PHP and MySQL are suitable
technologies for developing a web-based prototype for a competency-based
resource recommendation system.

## Objective 4: Testing and Evaluation of Educational Systems

To address the fourth objective of this study, this section reviews
literature on testing and evaluation methods for educational
recommendation systems.

Testing is a critical phase in system development, ensuring that the
system meets its requirements and functions as expected (Connolly and
Begg, 2015). For educational systems, user testing is particularly
important because the system\'s effectiveness depends on how well it
meets the needs of students and lecturers (Nabizadeh et al., 2020).

Dascalu et al. (2021) recommend validating educational recommendation
systems through user feedback and quantitative measures, such as
relevance scores. Testing should involve a small group of users who
interact with the system and provide feedback on its usability,
functionality, and perceived usefulness.

According to Obeid et al. (2024), rule-based recommendation systems can
be effectively evaluated by measuring the accuracy of recommendations
and user satisfaction. This aligns with the Technology Acceptance Model
(TAM), which emphasises the importance of perceived usefulness and
perceived ease of use in technology adoption (Davis, 1989).

In summary, the literature indicates that user testing and validation
are essential for confirming that an educational prototype meets its
requirements and provides value to its intended users.

**2.7.1 What Testing Entails**

Testing is the process of evaluating a system to ensure it meets its
requirements and functions as expected. According to Connolly and Begg
(2015), testing involves several phases:

Phase Description

Unit Testing: Testing individual components or modules of the system

Integration Testing: Testing how different modules work together

System Testing: Testing the entire system as a whole

User Acceptance Testing (UAT): Testing with end-users to ensure the
system meets their needs

Testing is important because it helps identify bugs, ensures
reliability, and builds user confidence in the system. Without proper
testing, a system may fail in real-world use, leading to user
frustration and loss of trust.

**2.7.2 Testing of Educational Systems**

Educational systems require special attention during testing because
they directly impact learning outcomes. According to Dascalu et al.
(2021), educational recommendation systems should be tested for:

· Relevance: Do the recommendations match the user\'s needs?

· Usability: Is the system easy to use?

· Performance: Does the system respond quickly?

· User Satisfaction: Are users happy with the system?

Studies by Nabizadeh et al. (2020) and Obeid et al. (2024) emphasize the
importance of involving real users (students and lecturers) in the
testing process to gather meaningful feedback. This approach, known as
user-centred testing, ensures that the system meets the actual needs of
its intended users.

For the proposed system, testing will involve:

1\. Functional Testing: Ensuring all features (login, resource upload,
recommendation) work correctly.

2\. Usability Testing: Observing students and lecturers as they interact
with the system to identify any difficulties.

3\. Accuracy Testing: Checking whether the system recommends relevant
resources based on the selected competency.

## 2.8 Review of Existing Educational Systems

Several systems are used in higher education for resource sharing:

  ------------------------------------------------------------------------------------------
  **System/Platform**   **System         **Linking           **Cost**      **Feasibility**
                        Competency**     Personalization**                 
  --------------------- ---------------- ------------------- ------------- -----------------
  Moodle                With plugins     Limited             Free          Moderate

  Google Classroom      No               No                  Free          Low

  Blackboard            Yes              Moderate            High          Low
                                                             (expensive)   

  Local university      Rarely           No                  Variable      Low
  portals                                                                  

  Library OPACs         No               No                  Varies        Low

  Proposed System       Yes (explicit)   Rule-based          Low           High
  ------------------------------------------------------------------------------------------

  : Table 1: Existing Educational Systems

*Source: Adapted from Mayisela (2019) and Kintu (2023)*

## 2.9 The Research Gap

After reviewing the literature, it is evident that while general-purpose
LMS and library systems exist, there is a distinct gap in systems that:

· Explicitly link resources to competencies within a course (Dascalu et
al., 2021).

· Provide a simple, database-driven interface for students to retrieve
resources based on their current learning needs (Obeid et al., 2024).

· Are built using accessible technologies (PHP, MySQL) that can be
maintained by local IT staff (Nixon, 2021).

· Are tailored to the specific competency structure of a university like
St. Lawrence University (Kaguhangire-Barifaijo, 2022).

This research aims to fill this gap by designing and prototyping such a
system. Unlike AI-based approaches, this system is feasible with the
researcher\'s current skill set and can serve as a model for other
institutions with similar constraints (Nabizadeh et al., 2020).

## 2.10 Chapter Summary

This chapter reviewed literature on CBE, ICT in education, personalised
recommendation systems, and database applications. The theoretical
framework (Constructivism and TAM) and conceptual framework were
presented. The review showed that while many systems exist, none provide
a simple, competency-linked resource recommendation system tailored to
St. Lawrence University. The gap is clear, and the proposed system is
well-positioned to address it. The next chapter describes the
methodology to be used to design and develop the system.

# CHAPTER THREE: METHODOLOGY

## 

## 3.3 Study Area

The study will be conducted at St. Lawrence University Uganda, located
in Kampala. The Faculty of Science and Technology was purposively
selected because it houses the Department of Informatics and
Engineering, where the researcher is enrolled, and where the CBC is
being implemented (Nannyonjo, 2021). This proximity facilitates access
to participants and documents.

## 3.4 Target Population

The target population comprises undergraduate students and lecturers in
the Faculty of Science and Technology. According to university records,
the faculty has approximately 600 students and 25 lecturers
(Kaguhangire-Barifaijo, 2022).

## 3.5 Sampling Strategy and Sample Size

A combination of purposive and simple random sampling will be used
(Saunders, Lewis and Thornhill, 2019):

· Lecturers: 5 lecturers from the Department of Informatics and
Engineering will be selected using purposive sampling. They are selected
because they are directly involved in teaching and resource sharing and
can provide rich insights.

· Students: 60 students (10% of the population) will be randomly
selected from different year groups using simple random sampling to
ensure fairness. This sample size is considered adequate for exploratory
research and for gathering requirements for the prototype.

## 

## 3.10 System Development Methodology

The System Development Life Cycle (SDLC) will be used to guide prototype
development (Connolly and Begg, 2015). The SDLC phases are:

1\. Requirements Analysis: Gathering requirements from students and
lecturers.

2\. System Design: Creating database schema and user interface.

3\. Implementation: Coding the prototype in PHP and MySQL.

4\. Testing & Validation: Testing with a small group of users and
gathering feedback.

## 3.11 Proposed System Design Tools

The proposed system will be designed and built using (Nixon, 2021;
Duckett, 2022):

· HTML/CSS/Bootstrap: For a responsive front-end user interface.

· PHP: For server-side logic (login, queries, and recommendations).

· MySQL: For data storage.

· Data Flow Diagrams (DFDs) & Entity-Relationship Diagrams (ERDs): For
modelling data flow and database structure (Connolly and Begg, 2015).

## 

# CHAPTER FOUR

# SYSTEM STUDY, ANALYSIS AND REQUIREMENTS ELICITATION

## 4.0 Introduction

This chapter presents a comprehensive study and analysis of the current
system used for accessing learning resources at St. Lawrence University
Uganda. The primary purpose of this chapter is to describe the existing
methods employed by students and lecturers, to identify the strengths
and weaknesses of these methods, and to elicit the requirements for the
proposed database-driven personalized learning resource recommendation
system. The chapter is structured into three main sections: the
description of the current system using a SWOT analysis approach, the
detailed requirements for the new system, and a summary of the findings.

## 4.1 Description of the Current System

Currently, students in the Faculty of Science and Technology at St.
Lawrence University access learning materials through a combination of
traditional and digital methods. The primary channels for resource
distribution include the university\'s Learning Management System (LMS)
which is Moodle-based, general-purpose platforms such as Google
Classroom, and the physical university library. Additionally, some
lecturers utilize WhatsApp groups and email to share supplementary
materials with their students.

In the existing system, lecturers upload course materials in bulk as PDF
documents, PowerPoint presentations, or external web links to these
platforms. These resources are typically organized by week or by general
course topic rather than by specific competencies. When a student needs
to find materials for a particular competency or a specific skill within
a course, they are required to scroll through these extensive lists of
resources, downloading multiple files before locating the relevant
information. The process is largely manual, requiring students to rely
on their own search skills and intuition to filter relevant content from
a large pool of generic materials. In the library, resources are
organized by subject area but lack any linkage to the specific
competency outcomes defined in the Competency-Based Curriculum (CBC).

## 4.1.1 Strengths of the Current System

Despite its limitations, the current system possesses several notable
strengths. Firstly, it provides a centralized repository where lecturers
can upload and store course materials for student access, ensuring that
students have a baseline level of resources available to them. Secondly,
the use of familiar platforms such as Moodle and Google Classroom
reduces the learning curve for both students and lecturers, as these are
widely used in higher education. Thirdly, the system supports a variety
of file formats, including PDFs, videos, and links, allowing lecturers
to share diverse types of learning content. Finally, the presence of
physical library resources ensures that students without reliable
internet access can still obtain learning materials for their studies.

## 4.1.2 Weaknesses of the Current System

The current system is characterized by several significant weaknesses
that undermine its effectiveness in supporting competency-based
education. A primary weakness is the lack of explicit linkage between
learning resources and specific competencies. Resources are uploaded in
bulk under general course headings, making it difficult for students to
identify materials that address their immediate learning needs. This
leads to substantial time wastage, as students must sift through
numerous files to find relevant content for a specific skill or topic.

Furthermore, the system does not provide personalized recommendations.
All students receive the same list of resources, regardless of their
individual progress or the specific competency they are currently
studying. This one-size-fits-all approach contradicts the philosophy of
competency-based education, which requires personalized learning
pathways. Additionally, there is no mechanism for lecturers to vet or
validate the resources that students find independently, which can lead
to the use of inaccurate or low-quality materials. The current system
also lacks a structured feedback mechanism, preventing students from
indicating which resources were most helpful for a particular
competency.

## 4.1.3 Comparative Analysis of the Strengths and Weaknesses

When comparing the strengths and weaknesses of the current system, it
becomes evident that the weaknesses substantially outweigh the
strengths. While centralized storage and familiarity with platforms are
convenient, these benefits are negated by the critical flaw of
non-alignment with the competency-based curriculum. The inability to
retrieve resources based on a specific competency directly hinders the
effective implementation of CBC at St. Lawrence University. The time
wasted by students searching for relevant materials and the risk of
engaging with unvetted resources represent significant inefficiencies
that reduce the overall quality of the learning experience. Therefore,
there is a clear and urgent need for a new system that addresses these
fundamental weaknesses.

## 4.2 Requirements of the New System

To address the weaknesses identified in the current system, the proposed
database-driven personalized learning resource recommendation system
must meet a specific set of requirements. These requirements are
categorized into user requirements, functional requirements,
non-functional requirements, and system requirements.

## 4.2.1 User Requirements

The users of the proposed system are primarily students and lecturers in
the Faculty of Science and Technology. The system must provide students
with an intuitive interface to easily search for and retrieve learning
resources based on their specific courses and competencies. Students
require the ability to view a filtered list of recommended resources
(links, videos, documents) that are directly tagged to the competency
they are currently studying.

Lecturers require a simple and efficient interface to upload, manage,
and tag learning resources to specific competencies and courses. They
also require the ability to review and edit resources that have been
uploaded. Additionally, lecturers need to verify that the resources
recommended to students are accurate, relevant, and of high quality. The
system must also accommodate an administrator who will manage user
accounts and oversee the overall functionality of the system.

## 4.2.2 Functional Requirements

The functional requirements define the specific tasks and operations
that the proposed system must perform:

1\. User Registration and Login: The system must allow students,
lecturers, and administrators to register and securely log in using a
username and password. Role-based access control must be implemented to
ensure that users only access features appropriate to their role (i.e.,
a student cannot upload resources like a lecturer).

2\. Course and Competency Selection: Students must be able to select a
specific course and then view a list of competencies associated with
that course. This is the primary input for generating recommendations.

3\. Resource Recommendation: Based on the selected competency, the
system must query the database and display a list of all learning
resources (title, type, and URL/description) that have been linked to
that specific competency by a lecturer.

4\. Resource Upload and Tagging: Lecturers must be able to upload new
learning resources (providing a title, type, and URL/uploaded file) and
explicitly tag them to a specific competency and course using a dropdown
or selection menu.

5\. Resource Management: Lecturers and administrators must be able to
edit, update, or delete resources they have uploaded.

6\. Search Functionality: The system should provide a basic search bar
that allows students and lecturers to search for resources using
keywords or competency names.

## 4.2.3 Non-Functional Requirements

The non-functional requirements describe the quality attributes and
performance constraints of the proposed system:

1\. Usability: The user interface must be simple, intuitive, and
responsive, ensuring that users with basic digital literacy can navigate
the system without extensive training. The design must be
mobile-friendly, considering that many students will access the system
via smartphones.

2\. Performance: The system must retrieve and display resource
recommendations within three (3) seconds of a user submitting a query.
The database queries must be optimized to handle multiple simultaneous
users efficiently.

3\. Security: All user passwords must be stored using a secure hashing
algorithm (e.g., bcrypt). Role-based access control (RBAC) must be
strictly enforced to prevent unauthorized access to administrative and
lecturer functions. The system must be protected against common web
vulnerabilities, such as SQL Injection and Cross-Site Scripting (XSS).

4\. Reliability and Availability: The system should be available 99% of
the time during operational hours. Regular backups of the database must
be scheduled to prevent data loss.

5\. Maintainability: The code must be well-structured, commented, and
modular, allowing a developer with basic PHP and MySQL knowledge to
update the system in the future.

## 4.2.4 System Requirements

To successfully host and run the proposed system, the university or the
hosting server must meet the following technical specifications:

1\. Web Server: Apache or Nginx.

2\. Server-Side Language: PHP (version 7.4 or higher).

3\. Database: MySQL (version 5.7 or higher) or MariaDB.

4\. Client-Side: A modern web browser (Chrome, Firefox, Safari) with
JavaScript enabled.

5\. Hardware: The server must have a minimum of 2GB RAM and sufficient
storage capacity to host the application files and the database.

##  4.3 Detailed System Description

The proposed system is a web-based application that will be accessed
through a browser. It will have three main user interfaces:

1\. Student Interface:

Login/Register page

Dashboard showing enrolled courses

Competency selection page for each course

Resource recommendation page displaying filtered resources

Search bar for keyword searches

2\. Lecturer Interface:

Login page

Dashboard showing courses taught

Resource upload page with tagging capabilities

Resource management page (edit/delete resources)

Course and competency management

3\. Administrator Interface:

Login page

User management page (add/edit/delete users)

System monitoring and logs

Database management

## 4.4 System Process Flow

The process flow for the proposed system is as follows:

A student logs in to the system.

The student selects a course from their enrolled courses.

The system displays all competencies associated with that course.

The student selects a specific competency.

The system queries the database for all resources tagged to that
competency.

The system displays a list of recommended resources (title, type,
description, and link/URL).

The student can click on any resource to access it.

The student can search for resources using keywords.

## 4.5 Use Cases of the Proposed System

Use Case 1: Student Views Recommended Resources

  ---------------------------------------------------------------------
  Element                            Description
  ---------------------------------- ----------------------------------
  Actor                              Student

  Precondition                       Student is logged in

  Trigger                            Student selects a course and
                                     competency

  Main Flow                          1\. Student logs in 2. Selects a
                                     course 3. Selects a competency 4.
                                     System displays recommended
                                     resources 5. Student views
                                     resources

  Postcondition                      Student has accessed relevant
                                     learning resources
  ---------------------------------------------------------------------

Use Case 2: Lecturer Uploads a Resource

  -----------------------------------------------------------------------
  Element                             Description
  ----------------------------------- -----------------------------------
  Actor                               Lecturer

  Precondition                        Lecturer is logged in

  Trigger                             Lecturer wants to share a resource

  Main Flow                           1\. Lecturer logs in 2. Selects a
                                      course 3. Selects a competency 4.
                                      Uploads resource and tags it 5.
                                      System saves the resource 6.
                                      Resource is available to students

  Postcondition                       Resource is stored and linked to
                                      the competency
  -----------------------------------------------------------------------

Use Case 3: Administrator Manages Users

  ----------------------------------------------------------------------
  Element                            Description
  ---------------------------------- -----------------------------------
  Actor                              Administrator

  Precondition                       Administrator is logged in

  Trigger                            Administrator needs to
                                     add/edit/delete a user

  Main Flow                          1\. Administrator logs in 2.
                                     Accesses user management 3.
                                     Adds/edits/deletes a user 4. System
                                     updates the database

  Postcondition                      User database is updated
  ----------------------------------------------------------------------

## 4.6 Chapter Summary

This chapter has provided a detailed analysis of the current system used
for accessing learning resources at St. Lawrence University. The
analysis revealed that while the current system provides centralized
access, its major weaknesses---including the lack of competency-based
resource tagging, inefficient resource retrieval, and absence of
personalized recommendations---significantly hinder the effective
implementation of Competency-Based Education. Through a comparative
analysis, it was established that the weaknesses outweigh the strengths,
justifying the need for a new system.

Consequently, this chapter elicited comprehensive user, functional,
non-functional, and system requirements for a database-driven
personalized learning resource recommendation system. The chapter also
included a detailed description of the proposed system, a process flow,
and use cases to illustrate how users will interact with the system.
These requirements and specifications will serve as the blueprint for
the design and implementation phases presented in the subsequent
chapter.

## APPENDIX V: SAMPLE DATABASE SCHEMA (SQL)

Users table

sql

CREATE TABLE users (

user_id INT PRIMARY KEY AUTO_INCREMENT,

username VARCHAR(50) UNIQUE NOT NULL,

password VARCHAR(255) NOT NULL,

role ENUM(\'student\', \'lecturer\', \'admin\') NOT NULL,

email VARCHAR(100)

);

Courses table

sql

CREATE TABLE courses (

course_id INT PRIMARY KEY AUTO_INCREMENT,

course_code VARCHAR(20) UNIQUE NOT NULL,

course_name VARCHAR(100) NOT NULL

);

Competencies table

sql

CREATE TABLE competencies (

competency_id INT PRIMARY KEY AUTO_INCREMENT,

course_id INT NOT NULL,

competency_name VARCHAR(200) NOT NULL,

FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE

);

Resources table

\`\`\`sql

CREATE TABLE resources (

resource_id INT PRIMARY KEY AUTO_INCREMENT,

competency_id INT NOT NULL,

resource_title VARCHAR(200) NOT NULL,

resource_type ENUM(\'link\', \'pdf\', \'video\', \'document\') NOT NULL,

resource_url VARCHAR(500),

uploaded_by INT NOT NULL,

upload_date DATE,

FOREIGN KEY (competency_id) REFERENCES competencies(competency_id) ON
DELETE CASCADE,

FOREIGN KEY (uploaded_by) REFERENCES users(user_id)

);
