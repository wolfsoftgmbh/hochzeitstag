<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unser Hochzeitstag</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>❤️</text></svg>" />
    
    <?php wp_head(); ?>
</head>
<body>

<div class="bg-shape shape-1"></div>
<div class="bg-shape shape-2"></div>

<div class="container">
    <div class="glass-card">
        <div class="content-wrapper">
            
            <header class="header-section">
                <h1>Unser Hochzeitstag <span style="font-size: 0.3em; opacity: 0.5; font-weight: normal; vertical-align: middle;">v<?php echo HOCHZEITSTAG_VERSION; ?></span></h1>
                <div id="quote-display" class="quote-box">
                    </div>
            </header>

            

            <div class="counter-grid">
                <div class="glass-circle">
                    <span class="number" id="val-years">0</span>
                    <span class="label">Jahre</span>
                </div>
                <div class="glass-circle">
                    <span class="number" id="val-days">0</span>
                    <span class="label">Tage</span>
                </div>
                <div class="glass-circle">
                    <span class="number" id="val-hours">0</span>
                    <span class="label">Std</span>
                </div>
                <div class="glass-circle">
                    <span class="number" id="val-minutes">0</span>
                    <span class="label">Min</span>
                </div>
            </div>

            <div class="section-title">Überraschungsidee</div>
            <div id="surprise-display" class="quote-box" style="margin-bottom: 2rem;">
                <!-- Random surprise idea will be inserted here -->
            </div>

            <div class="section-title">Nächste Meilensteine</div>
            <div class="timeline-container" id="milestone-list">
                <!-- Milestones will be inserted here by JavaScript -->
            </div>

            <div class="section-title">Unsere Geschichte</div>
            <div class="timeline-container history-mode" id="history-list">
                <!-- History items will be inserted here by JavaScript -->
            </div>

            <div class="footer-stats">
                <div class="stat-item">
                    <span id="total-days">0</span> Tage gemeinsam
                </div>
                <div class="stat-item">
                    <span id="total-seconds">0</span> Sekunden Liebe
                </div>
            </div>

            <footer class="footer-info">
                <div id="wedding-date-display" class="start-date"></div>
            </footer>
            
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
