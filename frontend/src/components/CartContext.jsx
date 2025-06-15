
import React, { createContext, useContext, useState, useEffect } from 'react';

const CartContext = createContext();
export const useCart = () => useContext(CartContext);

const getCartFromStorage = () => {
  const data = localStorage.getItem('cart');
  return data ? JSON.parse(data) : [];
};

const saveCartToStorage = (cart) => {
  localStorage.setItem('cart', JSON.stringify(cart));
};

export const CartProvider = ({ children }) => {
  const [cartItems, setCartItems] = useState(getCartFromStorage());

  useEffect(() => {
    saveCartToStorage(cartItems);
  }, [cartItems]);

  const addToCart = (product, selectedAttributes) => {
    const key = `${product.id}-${JSON.stringify(selectedAttributes)}`;

    setCartItems((prevItems) => {
      const existing = prevItems.find(
        (item) =>
          item.id === product.id &&
          JSON.stringify(item.selectedAttributes) === JSON.stringify(selectedAttributes)
      );

      if (existing) {
        return prevItems.map((item) =>
          item.id === product.id &&
          JSON.stringify(item.selectedAttributes) === JSON.stringify(selectedAttributes)
            ? { ...item, quantity: item.quantity + 1 }
            : item
        );
      } else {
        return [
          ...prevItems,
          {
            ...product,
            quantity: 1,
            selectedAttributes,
          },
        ];
      }
    });
  };

  const increase = (product) => {
    setCartItems((items) =>
      items.map((item) =>
        item === product ? { ...item, quantity: item.quantity + 1 } : item
      )
    );
  };

  const decrease = (product) => {
    setCartItems((items) =>
      items
        .map((item) =>
          item === product ? { ...item, quantity: item.quantity - 1 } : item
        )
        .filter((item) => item.quantity > 0)
    );
  };

  const clear = () => setCartItems([]);

  const total = cartItems.reduce(
    (acc, item) => acc + item.quantity * item.prices[0].amount,
    0
  );

  return (
    <CartContext.Provider
      value={{ cartItems, addToCart, increase, decrease, clear, total }}
    >
      {children}
    </CartContext.Provider>
  );
};
