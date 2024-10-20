const axios = require('axios');
const db = require('./db');

// Function to fetch book data from Google Books API
async function fetchBookData(isbn) {
    try {
        const response = await axios.get(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`);
        const bookData = response.data.items[0].volumeInfo;

        // Extract book information
        const title = bookData.title || null;
        const authors = bookData.authors ? bookData.authors.join(', ') : null;
        const publisher = bookData.publisher || null;

        // Extract only the year from publishedDate
        let publishedDate = bookData.publishedDate || null;
        if (publishedDate) {
            // If the date is in full format (YYYY-MM-DD or YYYY-MM), extract only the year
            publishedDate = publishedDate.split('-')[0];
        }

        // Debugging: Print the year to ensure it's formatted correctly
        console.log('Extracted published year:', publishedDate);

        const description = bookData.description || null;
        const pageCount = bookData.pageCount || null;
        const categories = bookData.categories ? bookData.categories.join(', ') : null;
        const language = bookData.language || null;

        // Insert into the Books table
        const sql = `
            INSERT INTO Books (title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        `;

        db.query(sql, [title, authors, publisher, publishedDate, description, pageCount, categories, language, isbn], (err, result) => {
            if (err) {
                console.error('Error inserting into the database:', err);
            } else {
                console.log('Book inserted into database:', result.insertId);
            }
        });

    } catch (error) {
        console.error('Error fetching book data:', error);
    }
}

// Example usage
fetchBookData('9782729805500');
