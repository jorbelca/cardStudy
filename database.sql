-- CREATE DATABASE IF NOT EXISTS api_rest_laravel;

-- USE api_rest_laravel;

USE sql11517816;

CREATE TABLE
    users(
        id INT(255) AUTO_INCREMENT NOT NULL,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATE,
        updated_at DATE,
        CONSTRAINT pk_users PRIMARY KEY(id)
    ) ENGINE = InnoDb;

CREATE TABLE
    questions(
        id INT(255) AUTO_INCREMENT NOT NULL,
        question VARCHAR(255) NOT NULL,
        correct_answer text,
        answers SET,
        topic_id INT(255) NOT NULL,
        n_correct INT(255) NOT NULL,
        n_incorrect INT(255) NOT NULL,
        creator_id INT(255) NOT NULL,
        created_at DATE,
        updated_at DATE,
        CONSTRAINT pk_questions PRIMARY KEY(id),
        CONSTRAINT fk_topic_id FOREIGN KEY(topic_id) REFERENCES topics(id),
        CONSTRAINT fk_creator_id FOREIGN KEY(creator_id) REFERENCES users(id)
    ) ENGINE = InnoDb;

CREATE TABLE
    topics(
        id INT(255) AUTO_INCREMENT NOT NULL,
        topic_name VARCHAR(255),
        created_at DATE,
        updated_at DATE,
        CONSTRAINT pk_topics PRIMARY KEY(id)
    ) ENGINE = InnoDb;

CREATE TABLE
    user_records (
        user_id INT(255) NOT NULL,
        question_id INT(255) NOT NULL,
        last_answer_correct BOOLEAN,
        last_date_shown DATETIME,
        next_date DATETIME,
        level INT(10),
        created_at DATE,
        updated_at DATE,
        CONSTRAINT fk_question_id FOREIGN KEY(question_id) REFERENCES questions(id),
        CONSTRAINT fk_user_id FOREIGN KEY(user_id) REFERENCES users(id)
    ) ENGINE = InnoDb;