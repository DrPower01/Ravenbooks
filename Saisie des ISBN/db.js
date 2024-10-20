// db.js
const mysql = require('mysql2');

// Create connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',    // Replace with your MySQL username
    password: '',    // Replace with your MySQL password
    database: 'library' // Replace with your database name
});

// Connect to MySQL
db.connect((err) => {
    if (err) {
        console.log('Error connecting to the database:', err);
        return;
    }
    console.log('Connected to the database');
});

module.exports = db;
