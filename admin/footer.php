        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $totalProduits ?? 0 ?></div>
                <div class="stat-label">Produits Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= isset($familles) ? count($familles) : 0 ?></div>
                <div class="stat-label">Familles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($valeurStock ?? 0, 2, ',', ' ') ?> €</div>
                <div class="stat-label">Valeur Stock (Prix d'achat)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($valeurVente ?? 0, 2, ',', ' ') ?> €</div>
                <div class="stat-label">Valeur Potentielle (Prix de vente)</div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="search-group">
                <input type="text" 
                       class="search-input" 
                       id="searchInput"
                       placeholder="Rechercher par nom, référence, famille..." 
                       value="<?= htmlspecialchars($recherche ?? '') ?>">
                
                <select class="select-famille" id="familleFilter">
                    <option value="">Toutes les familles</option>
                    <?php if (isset($familles)): ?>
                        <?php foreach ($familles as $famille): ?>
                            <option value="<?= htmlspecialchars($famille) ?>" 
                                    <?= ($familleSelectionnee ?? '') == $famille ? 'selected' : '' ?>>
                                <?= htmlspecialchars($famille) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                
                <button type="button" class="btn btn-primary" onclick="rechercherProduits()">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                
                <button type="button" class="btn btn-warning" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
            
            <div style="margin-left: auto;">
                <a href="ajouter.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nouveau Produit
                </a>
            </div>
        </div>

        <!-- Tableau des produits -->
        <div class="products-table">
            <div class="table-header">
                <div>ID</div>
                <div>Référence</div>
                <div>Famille</div>
                <div>Désignation</div>
                <div>Prix d'achat</div>
                <div>Prix de vente</div>
                <div>Actions</div>
            </div>
            
            <?php if (!isset($produits) || empty($produits)): ?>
                <div class="table-row" style="grid-template-columns: 1fr; text-align: center; color: var(--text-muted); font-style: italic;">
                    Aucun produit trouvé
                </div>
            <?php else: ?>
                <?php foreach ($produits as $index => $produit): ?>
                    <div class="table-row <?= $index % 2 === 1 ? 'table-row-alternate' : '' ?>">
                        <div class="product-code"><?= htmlspecialchars($produit['id']) ?></div>
                        <div class="product-code"><?= htmlspecialchars($produit['reference']) ?></div>
                        <div><strong><?= htmlspecialchars($produit['famille']) ?></strong></div>
                        <div class="product-description">
                            <div class="designation">
                                <?php if ($produit['est_compose'] ?? false): ?>
                                    <i class="fas fa-layer-group" title="Article composé" style="color: #007bff; margin-right: 5px;"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($produit['designation']) ?>
                                <?php if ($produit['est_compose'] ?? false): ?>
                                    <small style="color: #007bff; font-style: italic; margin-left: 5px;">(Composé)</small>
                                <?php endif; ?>
                            </div>
                            <div class="details">
                                <?php if (!empty($produit['format'])): ?>
                                    Format: <?= htmlspecialchars($produit['format']) ?> | 
                                <?php endif; ?>
                                <?php if (!empty($produit['papier'])): ?>
                                    Papier: <?= htmlspecialchars($produit['papier']) ?> | 
                                <?php endif; ?>
                                <?php if (!empty($produit['grammage'])): ?>
                                    Grammage: <?= htmlspecialchars($produit['grammage']) ?>g
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-prices">
                            <div class="prix-achat">
                                <?= number_format($produit['prixAchat'], 2, ',', ' ') ?> €
                            </div>
                        </div>
                        <div class="product-prices">
                            <div class="prix-vente">
                                <?= number_format($produit['prixVente'], 2, ',', ' ') ?> €
                            </div>
                        </div>
                        <div class="product-actions">
                            <?php if ($produit['est_compose'] ?? false): ?>
                                <a href="voir_composition.php?id=<?= $produit['id'] ?>" class="btn btn-info btn-sm" title="Voir la composition">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                            <a href="modifier.php?id=<?= $produit['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?action=supprimer&id=<?= $produit['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="pagination">
                <?php if (isset($pageActuelle) && $pageActuelle > 1): ?>
                    <a href="?page=<?= $pageActuelle - 1 ?>&recherche=<?= urlencode($recherche ?? '') ?>&famille=<?= urlencode($familleSelectionnee ?? '') ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, ($pageActuelle ?? 1) - 2); $i <= min(($totalPages ?? 1), ($pageActuelle ?? 1) + 2); $i++): ?>
                    <?php if ($i == ($pageActuelle ?? 1)): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?>&recherche=<?= urlencode($recherche ?? '') ?>&famille=<?= urlencode($familleSelectionnee ?? '') ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if (isset($pageActuelle) && isset($totalPages) && $pageActuelle < $totalPages): ?>
                    <a href="?page=<?= $pageActuelle + 1 ?>&recherche=<?= urlencode($recherche ?? '') ?>&famille=<?= urlencode($familleSelectionnee ?? '') ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function rechercherProduits() {
            const recherche = document.getElementById('searchInput').value;
            const famille = document.getElementById('familleFilter').value;
            
            let url = '?';
            if (recherche) url += 'recherche=' + encodeURIComponent(recherche) + '&';
            if (famille) url += 'famille=' + encodeURIComponent(famille) + '&';
            
            window.location.href = url.slice(0, -1); // Remove trailing &
        }

        function resetFilters() {
            window.location.href = '?';
        }

        // Recherche en temps réel avec Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                rechercherProduits();
            }
        });

        // Auto-submit sur changement de famille
        document.getElementById('familleFilter').addEventListener('change', rechercherProduits);
    </script>
</body>
</html>