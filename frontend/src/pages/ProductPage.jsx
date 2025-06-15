import React, { useState } from 'react';
import { useParams } from 'react-router-dom';
import { useQuery } from '@apollo/client';
import { GET_PRODUCT } from '../graphql/queries';
import './ProductPage.css';
import { useCart } from '../components/CartContext'; 

const ProductPage = () => {
  const { id } = useParams();
  const { loading, error, data } = useQuery(GET_PRODUCT, { variables: { id } });
  const [selectedImage, setSelectedImage] = useState(0);
  const [selectedAttributes, setSelectedAttributes] = useState({});

  const { addToCart } = useCart(); 

  if (loading) return <p>Loading...</p>;
  if (error || !data?.product) return <p>Error loading product</p>;

  const product = data.product;
  const allSelected = product.attributes.every(
    attr => selectedAttributes[attr.name]
  );

  const handleSelect = (name, value) => {
    setSelectedAttributes(prev => ({ ...prev, [name]: value }));
  };

  return (
    <div className="product-page">
      <div className="product-gallery" data-testid="product-gallery">
        <div className="product-thumbnails">
          {product.gallery.map((img, i) => (
            <img
              key={i}
              src={img}
              alt={`thumb-${i}`}
              onClick={() => setSelectedImage(i)}
            />
          ))}
        </div>
        <div className="product-main-image">
          <img src={product.gallery[selectedImage]} alt={product.name} />
        </div>
      </div>

      <div className="product-info">
        <h1>{product.name}</h1>

        {product.attributes.map(attr => (
          <div
            key={attr.id}
            className="attribute-block"
            data-testid={`product-attribute-${attr.name.toLowerCase()}`}
          >
            <p><strong>{attr.name}:</strong></p>
            <div className="attribute-options">
              {attr.items.map(item => {
                const isSelected = selectedAttributes[attr.name] === item.value;
                if (attr.type === 'swatch') {
                  return (
                    <div
                      key={item.id}
                      className={`swatch ${isSelected ? 'selected' : ''}`}
                      style={{ backgroundColor: item.value }}
                      onClick={() => handleSelect(attr.name, item.value)}
                    ></div>
                  );
                } else {
                  return (
                    <button
                      key={item.id}
                      className={isSelected ? 'selected' : ''}
                      onClick={() => handleSelect(attr.name, item.value)}
                    >
                      {item.value}
                    </button>
                  );
                }
              })}
            </div>
          </div>
        ))}

        <div className="price-label">PRICE:</div>
        <div className="product-price">
          {product.prices[0].currency.symbol}{product.prices[0].amount.toFixed(2)}
        </div>

        <button
          className="add-to-cart-btn"
          disabled={!allSelected}
          data-testid="add-to-cart"
          onClick={() => addToCart(product, selectedAttributes)} 
        >
          ADD TO CART
        </button>

        <div
          className="product-description"
          data-testid="product-description"
          dangerouslySetInnerHTML={{ __html: product.description }}
        />
      </div>
    </div>
  );
};

export default ProductPage;
