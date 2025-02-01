<?php
require_once "includes/GameEngine.php";
?>

<div class="location-page">


    <!-- Content Section -->
    <p>Explore the beauty of nature at Roshan Jal Wildlife Sanctuary, home to exotic wildlife and lush forests.</p>

    <!-- Placeholder for Wildlife Tours -->
    <div class="wildlife-tours">
        <h2>Wildlife Experiences</h2>
        <p>No tours or activities are available at the moment. Visit again soon!</p>
    </div>

    <!-- Back Button -->
    <div class="back-button">
        <a href="index.php?page=city">← Back to City</a>
    </div>
</div>

<style>


    .location-page {
        padding: 15px;
    }

    .location-page p {
        font-size: 1rem;
        color: #ccc;
    }

    .wildlife-tours {
        margin-top: 20px;
    }

    .wildlife-tours h2 {
        font-size: 1.5rem;
        color: #fff;
    }

    .wildlife-tours p {
        font-size: 1rem;
        color: #aaa;
    }

    .back-button {
        margin-top: 20px;
    }

    .back-button a {
        text-decoration: none;
        color: #fff;
        background-color: #444;
        padding: 8px 12px;
        border-radius: 5px;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .back-button a:hover {
        background-color: #555;
    }
</style>
