<?php
require_once "includes/GameEngine.php";
?>

<div class="city-page">
    <h1 class="page-title">Explore the City</h1>
    <div class="city-hub">
        <div class="city-grid">
            <!-- Vaddy -->
            <div class="city-area">
                <div class="city-header">
                    <h2>Vaddy</h2>
                    <p class="city-description">A bustling area known for its vibrant street markets and food joints.</p>
                </div>
                <div class="city-subgrid">
                    <div class="city-location">
                        <a href="index.php?page=fup_office">Freedom United Party Office</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=airport">Kalpanapur International Airport</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=jail">Vaddy Jail</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=charsi_gang">Charsi Gang</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=recycling_plant">Waste Recycling Plant</a>
                    </div>
                </div>
            </div>

            <!-- Shantipuram -->
            <div class="city-area">
                <div class="city-header">
                    <h2>Shantipuram</h2>
                    <p class="city-description">A serene neighborhood perfect for peaceful strolls.</p>
                </div>
                <div class="city-subgrid">
                    <div class="city-location">
                        <a href="index.php?page=bank">Bank of Kalpanapur</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=mall">Kalpanapur Mall</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=electronics_store">Electronics Store</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=museum">Museum</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=university">Kalpanapur University</a>
                    </div>
                </div>
            </div>

            <!-- Beach Marg -->
            <div class="city-area">
                <div class="city-header">
                    <h2>Beach Marg</h2>
                    <p class="city-description">A coastal paradise where the sun meets the sea.</p>
                </div>
                <div class="city-subgrid">
                    <div class="city-location">
                        <a href="index.php?page=clothing_store">Clothing Store</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=beach">Beach</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=joggers_park">Joggers's Park</a>
                    </div>
                </div>
            </div>

            <!-- Kalpanapur Square -->
            <div class="city-area">
                <div class="city-header">
                    <h2>Kalpanapur Square</h2>
                    <p class="city-description">The heart of the city filled with historical landmarks.</p>
                </div>
                <div class="city-subgrid">
                    <div class="city-location">
                        <a href="index.php?page=chor_bazaar">Chor Bazaar</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=sidhu_singh">Sidhu Singh</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=kfp_office">Kalpanapur Forward Party Office</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=grocery_store">Grocery Store</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=hospital">Kalpanapur Medical College</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=realty">Top Spot Realty</a>
                    </div>
                </div>
            </div>

            <!-- Jungle Valley -->
            <div class="city-area">
                <div class="city-header">
                    <h2>Jungle Valley</h2>
                    <p class="city-description">A dense forest with hidden mysteries waiting to be discovered.</p>
                </div>
                <div class="city-subgrid">
                    <div class="city-location">
                        <a href="index.php?page=bevda_gang">Bevda Gang</a>
                    </div>
                    <div class="city-location">
                        <a href="index.php?page=wildlife_sanctuary">Roshan Jal Wildlife Sanctuary</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* Namespace styles with .city-page to avoid global conflicts */
    .city-page {
        font-size: 14px; /* Reset font size for city page */
    }
    .city-location a {
    text-decoration: none;
    color: #ccc; /* Default text color */
    padding: 6px;
    background: #444; /* Default background color */
    border-radius: 5px;
    text-align: center;
    font-size: 0.85rem;
    display: block; /* Ensures the link behaves like a block element */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease; /* Smooth transition for hover effects */
}

.city-location a:link,
.city-location a:visited {
    color: #ccc; /* Ensure visited links look the same as unvisited links */
    background: #444; /* Same background for visited links */
}

.city-location a:hover {
    background-color: #555; /* Hover effect background */
    color: #fff; /* Hover effect text color */
    transform: translateY(-2px); /* Slight lift effect */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
}

.city-location a:active {
    background-color: #333; /* Slightly darker background for active links */
    color: #aaa; /* Slightly muted text color for active links */
    box-shadow: none; /* Remove shadow for a "pressed" look */
    transform: translateY(0); /* Reset position */
}

    .page-title {
        font-size: 1.8rem;
        color: #fff;
        text-align: left;
        margin-top: 10px;
        margin-left: 20px;
    }

    .city-hub {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
    }

    .city-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        width: 100%;
        max-width: 1200px;
    }

    .city-area {
        background: #2c2c2c;
        border-radius: 8px;
        padding: 15px;
        text-align: left;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    }

    .city-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .city-header h2 {
        font-size: 1.2rem;
        color: #fff;
        margin: 0;
    }

    .city-header .city-description {
        font-size: 0.85rem;
        color: #aaa;
        margin: 0;
    }

    .city-subgrid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 8px;
        margin-top: 10px;
    }


</style>