require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const controllQuantityButtons = () => {
    const decreaseButton = document.getElementById("quantity_decrease");
    const increaseButton = document.getElementById("quantity_increase");
    const visualQuantity = document.getElementById("quantity_text");
    const quantityForm = document.getElementById("quantity");

    // make sure buttons are not disabled when wanting to edit savedProduct
    if(quantityForm.value > 1) {
        decreaseButton.classList.remove("border-gray");
        decreaseButton.classList.remove("text-gray");
        decreaseButton.classList.add("border-pink");
        decreaseButton.classList.add("text-pink");
        decreaseButton.disabled = false;
    };

    decreaseButton.addEventListener("click", function(event) {
        event.preventDefault()

        quantityForm.value--
        visualQuantity.innerHTML = quantityForm.value;

        if(quantityForm.value == 1) {
            decreaseButton.classList.remove("border-pink");
            decreaseButton.classList.remove("text-pink");
            decreaseButton.classList.add("border-gray");
            decreaseButton.classList.add("text-gray");
            decreaseButton.disabled = true;
        };
    });

    increaseButton.addEventListener("click", function(event) {
        event.preventDefault();

        if(quantityForm.value == 1) {
            decreaseButton.classList.remove("border-gray");
            decreaseButton.classList.remove("text-gray");
            decreaseButton.classList.add("border-pink");
            decreaseButton.classList.add("text-pink");
            decreaseButton.disabled = false;
        };

        quantityForm.value++
        visualQuantity.innerHTML = quantityForm.value;
    });
}

const controllMenuButtons = () => {
    const openMenu = document.getElementById("open_menu");
    const menu = document.getElementById("menu");
    const closeMenu = document.getElementById("close_menu");

    openMenu.addEventListener("click", function(event) {
        event.preventDefault()

        menu.classList.remove("-right-80vw");
        menu.classList.add("right-0");
    });

    closeMenu.addEventListener("click", function(event) {
        event.preventDefault()

        menu.classList.remove("right-0");
        menu.classList.add("-right-80vw");
    });
}

const controllSortButtons = () => {
    const openSort = document.getElementById("open_sort");
    const sort = document.getElementById("menu_sort");
    const closeSort = document.getElementById("close_sort");

    const openFilter = document.getElementById("open_filter");
    const filter = document.getElementById("menu_filter");
    const closeFilter = document.getElementById("close_filter");

    const openCategories = document.getElementById("open_categories");
    const categoryMenu = document.getElementById("menu_categories");
    const closeCategories = document.getElementById("close_categories");


    openSort.addEventListener("click", function(event) {
        event.preventDefault()

        sort.classList.remove("hidden");
    });

    closeSort.addEventListener("click", function(event) {
        event.preventDefault()

        sort.classList.add("hidden");
    });

    openFilter.addEventListener("click", function(event) {
        event.preventDefault()

        filter.classList.remove("hidden");
    });

    closeFilter.addEventListener("click", function(event) {
        event.preventDefault()

        filter.classList.add("hidden");
    });

    openCategories.addEventListener("click", function(event) {
        event.preventDefault()

        categoryMenu.classList.remove("hidden");
    });

    closeCategories.addEventListener("click", function(event) {
        event.preventDefault()

        categoryMenu.classList.add("hidden");
    });
}

const controllScrapeFilterButtons = () => {
    const openShop = document.getElementById("open_shop_scrape");
    const shopMenu = document.getElementById("menu_shop_scrape");
    const closeShop = document.getElementById("close_shop_scrape");

    openShop.addEventListener("click", function(event) {
        event.preventDefault()

        shopMenu.classList.remove("hidden");
    });

    closeShop.addEventListener("click", function(event) {
        event.preventDefault()

        shopMenu.classList.add("hidden");
    });
}

const controllCopyButton = () => {
    const copyTextElement = document.getElementById("copy_text");
    const copyText = copyTextElement.innerHTML;
    const copyButton = document.getElementById("copy_button");

    copyButton.addEventListener("click", function(event) {
        event.preventDefault()

        navigator.clipboard.writeText(copyText);
        copyTextElement.innerHTML = "Copied!";
        setTimeout(function() {
            copyTextElement.innerHTML = copyText;
        }, 900);
    });
}

const controllWishlistButtons = () => {
    const wishlistOpen = document.getElementById("wishlist_open");
    const wishlistMenu = document.getElementById("wishlist_menu");
    const wishlistClose = document.getElementById("wishlist_close");

    wishlistOpen.addEventListener("click", function(event) {
        event.preventDefault()

        wishlistMenu.classList.remove("hidden");
    });

    wishlistClose.addEventListener("click", function(event) {
        event.preventDefault()

        wishlistMenu.classList.add("hidden");
    });
}

const controllStatus = () => {
    const status = document.getElementById("status");

    setTimeout(function() {
        status.classList.add("hidden");
    }, 1250);
}

const controllStatusWarning = () => {
    const statusWarning = document.getElementById("status-warning");

    setTimeout(function() {
        statusWarning.classList.add("hidden");
    }, 1250);
}

const controllImageButtons = () => {
    const mainImage = document.getElementById("main_image");
    const imgCount = document.getElementById("img_count");

    for(let i = 0; i < imgCount.value; i++) {
        const imgButton = document.getElementById(`img_button_${i}`);
        const imgTitle = document.getElementById(`img_title_${i}`);

        imgButton.addEventListener("click", function(event) {
            event.preventDefault()

            // remove class from all buttons
            for(let j = 0; j < imgCount.value; j++) {
                const imgButtonRemove = document.getElementById(`img_button_${j}`);
                imgButtonRemove.classList.remove("bg-green");
            }

            // add class to current button
            imgButton.classList.add("bg-green");

            // add correct src to mainImage
            mainImage.src = `${window.location.protocol}//${window.location.host}/storage/${imgTitle.value}`;
        });

    }
}



if(document.getElementById("quantity_decrease")) {
    controllQuantityButtons();
}

if(document.getElementById("open_menu")) {
    controllMenuButtons();
}

if(document.getElementById("open_sort")) {
    controllSortButtons();
}

if(document.getElementById("open_shop_scrape")) {
    controllScrapeFilterButtons();
}

if(document.getElementById("copy_button")) {
    controllCopyButton();
}

if(document.getElementById("wishlist_open")) {
    controllWishlistButtons();
}

if(document.getElementById("status")) {
    controllStatus();
}

if(document.getElementById("status-warning")) {
    controllStatusWarning();
}

if(document.getElementById("main_image")) {
    controllImageButtons();
}
