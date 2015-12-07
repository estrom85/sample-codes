using DataProvider.Interfaces;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.EntityModels.BackendModel;

namespace DataProvider.DataModel.BackendModel
{
    public class BEPOI : IPOI
    {
        private POIS _poi;

        public BEPOI(POIS poi)
        {
            _poi = poi;
        }

        #region COPMARE_LOGIC_DEFINITION
        public override bool Equals(object obj)
        {
            BEPOI other = obj as BEPOI;
            if (other != null)
            {
                return _poi.Equals(other._poi);
            }
            return false;
        }

        public override int GetHashCode()
        {
            return _poi.GetHashCode();
        }

        #endregion COMPARE_LOGIC_DEFINITION

        public int ID
        {
            get { return _poi.ID; }
        }

        public string SYSTEM_UUID
        {
            get { return _poi.SYSTEM_UUID; }
        }

        public string NAME
        {
            get { return _poi.NAME; }
        }

        public string DESCRIPTION
        {
            get { return _poi.DESCRIPTION; }
        }

        public decimal? LONGITUDE
        {
            get { return (decimal?)_poi.LONGITUDE; }
        }

        public decimal? LATITUDE
        {
            get { return (decimal?)_poi.LATITUDE; }
        }

        public DateTime? CREATED_AT
        {
            get { return _poi.CREATED_AT; }
        }

        public DateTime? UPDATED_AT
        {
            get { return _poi.UPDATED_AT; }
        }

        private ICollection<IProduct> _products = null;
        public ICollection<IProduct> PRODUCTS
        {
            get 
            {
                if (_products == null)
                {
                    _products = new HashSet<IProduct>();
                    foreach (PRODUCTS prod in _poi.PRODUCTS)
                    {
                        _products.Add(new BEProduct(prod));
                    }
                }
                return _products;
            }
        }

        private ICollection<IMedia> _media = null;
        public ICollection<IMedia> MEDIA
        {
            get 
            { 
                if (_media == null)
                {
                    _media = new HashSet<IMedia>();
                    foreach (MEDIA prod in _poi.MEDIA)
                    {
                        _media.Add(new BEMedia(prod));
                    }
                }
                return _media;
            }
        }
    }
}
