body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f0f0f5;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: #ffffff;
    border-bottom: 1px solid #ddd;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

nav a {
    margin: 0 15px;
    text-decoration: none;
    color: black;
    font-weight: bold;
}

main {
    padding: 20px;
    text-align: center;
}

h1 {
    margin-bottom: 20px;
    font-size: 32px;
}

.product-grid {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.product {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    width: 200px;
    text-align: center;
}

.product img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.product h2 {
    font-size: 18px;
    margin: 10px 0;
}

.product p {
    font-size: 16px;
    color: #555;
}

button {
    background-color: black;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

button:first-of-type {
    background-color: #e0e0e0;
    color: black;
}

button:hover {
    opacity: 0.8;
}
