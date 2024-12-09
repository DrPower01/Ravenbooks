<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        nav ul {
            display: flex;
            list-style: none;
            justify-content: center;
        }

        nav ul li {
            margin: 0 1rem;
            position: relative;
        }

        nav ul li a {
            text-decoration: none;
            color: black; /* Set default color to white */
            font-weight: bold;
            transition: color 0.3s;
        }

        /* Add hover effect to turn text color to white */
        nav ul li a:hover {
            color: white;
        }

        /* Dropdown menu styles */
        .drop-down {
            position: absolute;
            top: 100%; /* Position juste en dessous du lien parent */
            left: 0;
            padding: 0.5rem 0;
            margin: 0;
            background: linear-gradient(to right, #dce1e6, #fce7e7);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            font-size: 12px;
            list-style: none;
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        .drop-down li {
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }

        .drop-down li i {
            margin-right: 0.5rem;
            vertical-align: middle;
        }

        /* Hover effect for dropdown items */
        .drop-down li:hover {
            background: black;
            color: white;
            border-radius: 0.5rem;
        }

        /* Show dropdown on hover */
        .Lieux:hover .drop-down,
        .Catalogue:hover .drop-down {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .Lieux a,
        .Catalogue a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: black; /* Set text color for links in dropdown to white */
        }

        .Lieux .dropdown-icon,
        .Catalogue .dropdown-icon {
            margin-left: 0.5rem;
            font-size: 14px;
            transform: rotate(0deg);
            transition: transform 0.3s ease-in-out;
        }

        .Lieux:hover .dropdown-icon,
        .Catalogue:hover .dropdown-icon {
            transform: rotate(180deg);
        }

        .btn-primary {
            display: inline-block;
            text-decoration: none;
            background: #ff6f61;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 30px;
            font-size: 1.1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #ff4a36;
            transform: translateY(-2px);
        }
    </style>
</head>

<!-- Navbar -->
<nav>
    <ul>
        <li><a href="Home(unfinished).php">Home</a></li>

        <!-- Catalogue Dropdown -->
        <li class="Catalogue">
            <a href="#">Catalogue
                <span class="material-icons dropdown-icon">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>
            <ul class="drop-down">
                <a href="filtrageParGenre.php"><li>Par genre</a></li>
                <a href="filtrageParLettre.php"><li>Par lettre</a></li>
            </ul>
        </li>

        
        <li class="Lieux">
            <a href="#">Lieux
                <span class="material-icons dropdown-icon">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>
            <ul class="drop-down">
                <li><i class="fa-solid fa-graduation-cap"></i> Université de Balbal</li>
                <li><i class="fa-brands fa-squarespace"></i> Institut Nationale</li>
                <li><i class="fa-solid fa-building-columns"></i> Archives Nationales</li>
            </ul>
        </li>

        <li class="Lieux">
            <a href="#">Admin
                <span class="material-icons dropdown-icon">
                    <i class="fa-solid fa-caret-down"></i>
                </span>
            </a>
            <ul class="drop-down">
                <li><a href=""><i class="fas fa-plus"></i> Ajouter un livre</a></li>
                <li><a href=""><i class="fas fa-sync-alt"></i> Mise a jour</a></li>
                <li><a href=""><i class="fas fa-edit"></i> Modification</a></li>
                <li><a href=""><i class="fas fa-trash-alt"></i> Suppression</a></li>
            </ul>
        </li>

        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
    </ul>
</nav>
