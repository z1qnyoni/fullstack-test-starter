import { gql } from '@apollo/client';


export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;


export const GET_PRODUCTS = gql`
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      gallery
      description
      category
      brand
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          label
          symbol
        }
      }
    }
  }
`;


export const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      category
      brand
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          label
          symbol
        }
      }
    }
  }
`;