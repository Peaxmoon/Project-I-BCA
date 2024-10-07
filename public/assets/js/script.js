document.addEventListener('DOMContentLoaded', () => {
    fetchMenu();
    document.getElementById('place-order').addEventListener('click', placeOrder);
});

async function fetchMenu() {
    try {
        const response = await fetch('api.php?action=getMenu');
        const menuItems = await response.json();
        displayMenu(menuItems);
    } catch (error) {
        console.error('Error fetching menu:', error);
    }
}

function displayMenu(menuItems) {
    const menuContainer = document.getElementById('menu-items');
    menuItems.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.innerHTML = `
            <h3>${item.name}</h3>
            <p>${item.description}</p>
            <p>Price: $${item.price}</p>
            <button onclick="addToOrder(${item.item_id}, '${item.name}', ${item.price})">Add to Order</button>
        `;
        menuContainer.appendChild(itemElement);
    });
}

let orderItems = [];

function addToOrder(itemId, name, price) {
    orderItems.push({ itemId, name, price });
    updateOrderDisplay();
}

function updateOrderDisplay() {
    const orderContainer = document.getElementById('order-items');
    orderContainer.innerHTML = '';
    orderItems.forEach((item, index) => {
        const itemElement = document.createElement('div');
        itemElement.innerHTML = `
            <p>${item.name} - $${item.price}</p>
            <button onclick="removeFromOrder(${index})">Remove</button>
        `;
        orderContainer.appendChild(itemElement);
    });
}

function removeFromOrder(index) {
    orderItems.splice(index, 1);
    updateOrderDisplay();
}

async function placeOrder() {
    if (orderItems.length === 0) {
        alert('Your order is empty!');
        return;
    }

    const totalAmount = orderItems.reduce((total, item) => total + item.price, 0);
    const orderData = {
        customer_id: 1, // In a real app, you'd get this from user authentication
        table_id: 1, // In a real app, you'd get this from the QR code scan
        total_amount: totalAmount,
        items: orderItems
    };

    try {
        const response = await fetch('api.php?action=placeOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData),
        });
        const result = await response.json();
        if (result.success) {
            alert(`Order placed successfully! Order ID: ${result.order_id}`);
            orderItems = [];
            updateOrderDisplay();
        } else {
            alert('Failed to place order. Please try again.');
        }
    } catch (error) {
        console.error('Error placing order:', error);
        alert('An error occurred while placing the order. Please try again.');
    }
}