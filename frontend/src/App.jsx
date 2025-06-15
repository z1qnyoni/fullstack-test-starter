import React, { useState } from 'react';
import { Routes, Route } from 'react-router-dom';
import CategoryPage from './pages/CategoryPage';
import ProductPage from './pages/ProductPage';
import Header from './components/Header';
import CartOverlay from './components/CartOverlay';
import { useCart } from './components/CartContext';

const App = () => {
  const [isCartOpen, setIsCartOpen] = useState(false);
  const { cartItems, increase, decrease, total, clear } = useCart();

  return (
    <>
      <Header onCartClick={() => setIsCartOpen(prev => !prev)} cartCount={cartItems.reduce((acc, item) => acc + item.quantity, 0)} />

      <Routes>
        <Route path="/" element={<CategoryPage />} />
        <Route path="/product/:id" element={<ProductPage />} />
      </Routes>

      {isCartOpen && (
        <CartOverlay
          cartItems={cartItems}
          onIncrease={increase}
          onDecrease={decrease}
          total={total}
          onPlaceOrder={() => {
            clear();
            setIsCartOpen(false);
          }}
        />
      )}
    </>
  );
};

export default App;
