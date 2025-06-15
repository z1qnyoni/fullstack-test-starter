// Header.jsx
import React from 'react';
import { useQuery } from '@apollo/client';
import { GET_CATEGORIES } from '../graphql/queries';
import { Link, useLocation } from 'react-router-dom';
import './Header.css';

const Header = ({ onCartClick, cartCount }) => {
  const { data, loading, error } = useQuery(GET_CATEGORIES);
  const location = useLocation();
  const currentParams = new URLSearchParams(location.search);
  const activeCategory = currentParams.get('category') || 'all';

  if (loading) return <header>Loading...</header>;
  if (error) return <header>Error loading categories</header>;

  return (
    <header className="header">
      <div className="header-content">
        
        <nav className="nav">
          {data.categories.map((cat) => {
            const isActive = activeCategory === cat.name;
            return (
              <Link
                key={cat.name}
                to={`/?category=${cat.name}`}
                data-testid={isActive ? 'active-category-link' : 'category-link'}
                className={isActive ? 'active' : ''}
              >
                {cat.name.toUpperCase()}
              </Link>
            );
          })}
        </nav>

        
        <div className="logo">
          <img src="/logo.svg" alt="Logo" style={{ height: '24px' }} />
        </div>

        
        <div className="cart" data-testid="cart-btn" onClick={onCartClick}>
          <img src="/cart-icon.svg" alt="Cart" />
          {cartCount > 0 && <div className="cart-badge">{cartCount}</div>}
        </div>
      </div>
    </header>
  );
};

export default Header;