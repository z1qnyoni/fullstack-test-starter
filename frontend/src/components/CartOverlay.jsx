import React from 'react';
import './CartOverlay.css';

const CartOverlay = ({ cartItems, onIncrease, onDecrease, total, onPlaceOrder }) => {
  return (
    <div className="cart-overlay">
      <h2 className="cart-title">
        My Bag, {cartItems.length} {cartItems.length === 1 ? 'Item' : 'Items'}
      </h2>

      <div className="cart-items">
        {cartItems.map((item, index) => (
          <div className="cart-item" key={index}>
            <div className="cart-item-info">
              <p className="item-name">{item.name}</p>
              <p className="item-price">
                {item.prices[0].currency.symbol}
                {item.prices[0].amount.toFixed(2)}
              </p>

              {item.attributes.map(attr => (
                <div
                  key={attr.id}
                  data-testid={`cart-item-attribute-${attr.name.toLowerCase()}`}
                  className="cart-attribute-block"
                >
                  <p>{attr.name}:</p>
                  <div className="cart-attribute-options">
                    {attr.items.map(val => {
                      const selected = item.selectedAttributes[attr.name] === val.value;
                      const baseId = `cart-item-attribute-${attr.name.toLowerCase()}-${attr.name.toLowerCase()}`;
                      const testId = selected ? `${baseId}-selected` : baseId;
                      return (
                        <button
                          key={val.id}
                          data-testid={testId}
                          className={`cart-attr-btn ${selected ? 'selected' : ''}`}
                          style={
                            attr.type === 'swatch'
                              ? {
                                  backgroundColor: val.value,
                                  width: '24px',
                                  height: '24px',
                                  border: '1px solid #ccc'
                                }
                              : {}
                          }
                        >
                          {attr.type !== 'swatch' ? val.value : ''}
                        </button>
                      );
                    })}
                  </div>
                </div>
              ))}
            </div>

            <div className="cart-item-controls">
              <button
                data-testid="cart-item-amount-increase"
                onClick={() => onIncrease(item)}
              >
                +
              </button>
              <div data-testid="cart-item-amount">{item.quantity}</div>
              <button
                data-testid="cart-item-amount-decrease"
                onClick={() => onDecrease(item)}
              >
                -
              </button>
            </div>

            <div className="cart-item-image">
              <img src={item.gallery[0]} alt={item.name} />
            </div>
          </div>
        ))}
      </div>

      <div className="cart-summary">
        <span>Total:</span>
        <span data-testid="cart-total">
          {cartItems[0]?.prices[0].currency.symbol}{total.toFixed(2)}
        </span>
      </div>

      <button
        className="place-order-btn"
        onClick={onPlaceOrder}
        disabled={cartItems.length === 0}
      >
        PLACE ORDER
      </button>
    </div>
  );
};

export default CartOverlay;
