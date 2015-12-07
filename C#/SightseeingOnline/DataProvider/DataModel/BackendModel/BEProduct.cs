using DataProvider.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;

namespace DataProvider.DataModel.BackendModel
{
    public class BEProduct : IProduct
    {
        private PRODUCTS _product;

        public BEProduct(PRODUCTS product)
        {
            _product = product;
        }

        #region COMPARE_LOGIC_DEFINITION

        public override bool Equals(object obj)
        {
            if (obj is BEProduct)
            {
                return _product.Equals(((BEProduct)obj)._product);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _product.GetHashCode();
        }

        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _product.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _product.SYSTEM_UUID; }
        }

        public int? FRONTEND_ID
        {
            get { return _product.FRONTEND_ID; }
        }

        public string NAME
        {
            get { return _product.NAME; }
        }

        public int? QUANTITY
        {
            get { return _product.QUANTITY; }
        }

        public decimal? PRICE
        {
            get { return (decimal?)_product.PRICE; }
        }

        public decimal? MIN_PRICE
        {
            get { return (decimal?)_product.MIN_PRICE; }
        }

        public int? MEDIUM_ID
        {
            get { return _product.MEDIUM_ID; }
        }

        public decimal? AVERAGE_RATING
        {
            get { return (decimal?)_product.AVERAGE_RATING; }
        }

        public int? HITS
        {
            get { return _product.HITS; }
        }

        public string SHORT_DESCRIPTION
        {
            get { return _product.SHORT_DESCRIPTION; }
        }

        public string DESCRIPTION
        {
            get { return _product.DESCRIPTION; }
        }

        public DateTime? CREATED_AT
        {
            get { return _product.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _product.UPDATED_AT; }
        }

        public bool PUBLISHED
        {
            get { return _product.PUBLISHED.HasValue ? _product.PUBLISHED != 0 : false; }
        }

        public bool DELETED
        {
            get
            {
                return _product.DELETED_AT.HasValue;
            }
        }

        private ICollection<ICategory> _categories = null;
        public ICollection<ICategory> CATEGORIES
        {
            get 
            {
                if (_categories == null)
                {
                    _categories = new HashSet<ICategory>();
                    foreach (PRODUCTCATEGORIES cat in _product.PRODUCTCATEGORIES)
                    {
                        _categories.Add(new BECategory(cat));
                    }
                }
                return _categories;
            }
        }

        private ICollection<IVariant> _variants = null;
        public ICollection<IVariant> VARIANTS
        {
            get 
            {
                if (_variants == null)
                {
                    _variants = new HashSet<IVariant>();
                    foreach (PRODUCTVARIANTS variant in _product.PRODUCTVARIANTS)
                    {
                        _variants.Add(new BEVariant(variant));
                    }
                }
                return _variants;
            }
        }

        private ICollection<IReview> _reviews = null;
        public ICollection<IReview> REVIEWS
        {
            get 
            {
                if (_reviews == null)
                {
                    _reviews = new HashSet<IReview>();
                    foreach (PRODUCTREVIEWS rev in _product.PRODUCTREVIEWS)
                    {
                        //_reviews.Add(new BEReview(rev));
                    }
                }
                return _reviews;
            }
        }

        private ICollection<IPOI> _pois = null;
        public ICollection<IPOI> POIS
        {
            get 
            {
                if (_pois == null)
                {
                    _pois = new HashSet<IPOI>();
                    foreach (POIS poi in _product.POIS)
                    {
                        _pois.Add(new BEPOI(poi));
                    }
                }
                return _pois;
            }
        }

        private IMedia _media;
        public IMedia MEDIA
        {
            get
            {
                if (_media == null)
                {
                    if (_product.MEDIA == null)
                    {
                        return null;
                    }
                    _media = new BEMedia(_product.MEDIA);
                }
                return _media;
            }
        }
    }
}
