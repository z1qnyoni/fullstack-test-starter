import React from 'react';
import { useSearchParams, Link } from 'react-router-dom';
import { useQuery } from '@apollo/client';
import { GET_PRODUCTS } from '../graphql/queries';
import './CategoryPage.css';

const CategoryPage = () => {
  const [searchParams] = useSearchParams();
  const category = searchParams.get('category') || 'all';

  const { loading, error, data } = useQuery(GET_PRODUCTS, {
    variables: { category },
  });

  if (loading) return <p className="category-wrapper">Loading...</p>;
  if (error) return <p className="category-wrapper">Error loading products</p>;

  return (
    <main className="category-wrapper">
      <h2 className="category-title">{category.toUpperCase()}</h2>
      <div className="product-grid">
        {data.products.map((product) => (
          <Link
            to={`/product/${product.id}`}
            key={product.id}
            className="product-card"
            data-testid={`product-${product.name.toLowerCase().replace(/\s/g, '-')}`}
          >
            <div style={{ position: 'relative' }}>
              <img
                src={product.gallery[0]}
                alt={product.name}
                style={{
                  filter: product.inStock ? 'none' : 'grayscale(1)',
                }}
              />
              {!product.inStock && (
                <span className="out-of-stock">OUT OF STOCK</span>
              )}
            </div>
            <div>
              <p style={{ fontSize: '18px' }}>{product.name}</p>
              <p style={{ fontWeight: 500 }}>
                {product.prices[0].currency.symbol}
                {product.prices[0].amount.toFixed(2)}
              </p>
            </div>
          </Link>
        ))}
      </div>
    </main>
  );
};

export default CategoryPage;