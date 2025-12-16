<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Produits</title>
    
    <!-- Polices et styles -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin-fixed.css">
    
    <style>
        :root {
            --primary-dark: #2A256D;
            --primary-orange: #F05124;
            --background-light: #f8f9fa;
            --border-color: #dee2e6;
            --text-dark: #333;
            --text-muted: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* Header Admin */
        .admin-header {
            background: var(--primary-dark);
            color: white;
            padding: 15px 0;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 1000;
        }
        
        .admin-header .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            font-size: 24px;
            font-weight: 500;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 400;
            transition: color 0.2s ease;
        }
        
        .admin-nav a:hover {
            color: var(--primary-orange);
        }
        
        /* Container principal */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
            margin-top: 10px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        /* Toolbar */
        .toolbar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            width: 250px;
            background: white !important;
        }
        
        .select-famille {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            width: 200px;
            background: white !important;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: var(--primary-dark);
            color: white;
        }
        
        .btn-primary:hover {
            background: #1f1b5a;
        }
        
        .btn-success {
            background: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: var(--text-dark);
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        /* Tableau des produits */
        .products-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .table-header {
            background: var(--primary-dark);
            color: white;
            padding: 15px 20px;
            font-weight: 500;
            display: grid;
            grid-template-columns: 80px 120px 200px 1fr 100px 100px 150px;
            gap: 15px;
        }
        
        .table-row {
            padding: 2px 20px;
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 80px 120px 200px 1fr 100px 100px 150px;
            gap: 15px;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        
        .table-row:hover {
            background: #f8f9ff;
        }
        
        .table-row:last-child {
            border-bottom: none;
        }
        
        .table-row-alternate {
            background: #eeeeee;
        }
        
        .table-row-alternate:hover {
            background: #e8e8ff;
        }
        
        .product-code {
            font-family: 'Roboto Mono', monospace;
            font-weight: 500;
            font-size: 12px;
        }
        
        .product-description {
            line-height: 1.4;
        }
        
        .product-description .designation {
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .product-description .details {
            font-size: 11px;
            color: var(--text-muted);
        }
        
        .product-prices {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 12px;
        }
        
        .prix-achat {
            color: var(--text-muted);
        }
        
        .prix-vente {
            color: var(--primary-orange);
            font-weight: 500;
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        .pagination a:hover {
            background: var(--primary-dark);
            color: white;
        }
        
        .pagination .current {
            background: var(--primary-dark);
            color: white;
        }
        
        /* Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-dark);
        }
        
        .stat-label {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .table-header,
            .table-row {
                grid-template-columns: 70px 100px 180px 1fr 90px 90px 120px;
                gap: 10px;
            }
        }
        
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-group {
                flex-direction: column;
            }
            
            .search-input,
            .select-famille {
                width: 100%;
            }
            
            .table-header,
            .table-row {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: left;
            }
            
            .table-header > div,
            .table-row > div {
                padding: 5px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header Admin -->
    <header class="admin-header">
        <div class="header-container">
            <h1><i class="fas fa-cogs"></i> Administration et Gestion</h1>
            <nav class="admin-nav">
                <a href="index.php"><i class="fas fa-boxes"></i> Produits</a>
                <a href="gestion-clients.php"><i class="fas fa-users"></i> Clients</a>
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php 
            unset($_SESSION['message'], $_SESSION['message_type']); 
            endif; 
        ?>