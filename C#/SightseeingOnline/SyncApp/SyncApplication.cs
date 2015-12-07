using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Configuration;
using System.Collections.Specialized;
using DataProvider.Providers;
using System.Timers;
using DataProvider.Enumerations;
using System.ServiceModel;
using SyncWebService;
using Logging;
using SyncApp.Configuration;
using System.Text.RegularExpressions;

namespace SyncApp
{
    public class SyncApplication : ILogger
    {
        private FrontendDataProvider _frontend = new FrontendDataProvider();
        private BackendDataProvider _backend = new BackendDataProvider();
        
        private Timer _timer;

        private const long MILISECONDS_PER_DAY = 24 * 60 * 60 * 1000;

        private ConfigManager _configuration;

        private List<ILogger> _loggers = new List<ILogger>();

        private ServiceManager manager;

        public SyncApplication(ILogger logger = null)
        {
            if (logger != null)
            {
                _loggers.Add(logger);
            }
            _configuration = new ConfigManager();
            _timer = new Timer();
            _timer.Elapsed += _timer_Elapsed;
            manager = ServiceManager.GetInstance(this);
            manager.DataProvider = _backend;
            
        }

        public string GetHomeDirectory() { return _configuration.HomeDirectory; }

        private void _timer_Elapsed(object sender, ElapsedEventArgs e)
        {
            resetTimer();
           // WriteMessage(DateTime.Now, "Timer", "CurrentInterval:" + _timer.Interval);
            DateTime lastSync = DateTime.UtcNow;
            _synchronize();
           // _configuration.SetLastSync(lastSync);

            WriteMessage(DateTime.UtcNow,"Timer",String.Format("Last synchronisation: {0}. Next synchronisation: {1}",
                lastSync.ToShortTimeString(),lastSync.Add(_configuration.Time).ToShortTimeString()));
        }

        private void resetTimer()
        {
            if (_configuration.TimerType == TimerType.INTERVAL) return;
            if (_timer.Interval == MILISECONDS_PER_DAY) return;
            _timer.Stop();
            _setUpTimer();
            _timer.Start();
            WriteMessage(DateTime.Now, "Timer", "CurrentInterval:" + _timer.Interval);
        }
        
        public void StartService()
        {
            resetConfiguration();
            _setUpTimer();
            _timer.Start();
            manager.Start();
        }

        public void StopService()
        {
            manager.Stop();
            _timer.Stop();
        }

        private void _setUpTimer()
        {
            if (_configuration.TimerType == TimerType.INTERVAL)
            {
                _timer.Interval = (int)_configuration.Time.TotalMilliseconds;
                return;
            }
            long now = (long)DateTime.UtcNow.TimeOfDay.TotalMilliseconds / 1000;
            WriteMessage(DateTime.UtcNow, "Timer", "Current Time: " + DateTime.Now.TimeOfDay.ToString("c"));
            long interval = (long)_configuration.Time.TotalMilliseconds / 1000;
            WriteMessage(DateTime.UtcNow, "Timer", "Set Time: " + _configuration.Time.ToString("c"));

            if (now > interval)
            {
                _timer.Interval = MILISECONDS_PER_DAY - (now - interval) * 1000;
            }
            else if (now < interval)
            {
                _timer.Interval = (interval - now) * 1000;
            }
            else
            {
                _timer.Interval = MILISECONDS_PER_DAY;
            }
        }

        public void SubscribeLogger(ILogger logger)
        {
            _loggers.Add(logger);
        }

        public void UnsubscribeLogger(ILogger logger)
        {
            _loggers.Remove(logger);
        }

        public void _synchronize()
        {
            int[] statistics = new int[4];
            int success = 0;
            int failed = 0;
            #region SYNC_PRODUCTS
            
            WriteMessage(DateTime.Now, "Sync", "Synchronisation started");

            try
            {
                //synchronize categories
                _frontend.UpdateCategories(
                    _backend.GetCategories(_configuration.LastSync, Role.NEW_OR_CHANGED),
                    _backend.AssignCategoryFrontendID, statistics);
                WriteMessage(DateTime.Now, "Sync",
                    String.Format("Categories synchronisation finished. {0} categories changed[{1},{2}].", statistics[0], statistics[1], statistics[2]));
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Categories synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                //synchronise variant values 
                _frontend.UpdateVariantValues(
                    _backend.GetVariantValues(_configuration.LastSync, Role.NEW_OR_CHANGED),
                    _backend.AssignVariantValueFrontendID, statistics);

                WriteMessage(DateTime.Now, "Sync",
                   String.Format("Variant values synchronisation finished. {0} values changed[{1},{2}].", statistics[0], statistics[1], statistics[2]));
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Variant values synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _backend.UpdateProducts(_frontend.GetProducts(_configuration.LastSync));

                _frontend.UpdateTours(
                    _backend.GetTours(_configuration.LastSync, Role.NEW_OR_CHANGED),
                    _backend.AssignTourFrontendID, statistics);

                WriteMessage(DateTime.Now, "Sync",
                   String.Format("Tours synchronisation finished. {0} tours changed[{1},{2}].", statistics[0], statistics[1], statistics[2]));
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Tours synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _frontend.UpdateVariants(
                    _backend.GetVariants(_configuration.LastSync, Role.NEW_OR_CHANGED),
                    _backend.AssignVariantFrontendID, statistics);

                WriteMessage(DateTime.Now, "Sync",
                   String.Format("Variants synchronisation finished. {0} variants changed[{1},{2}].", statistics[0], statistics[1], statistics[2]));
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Variants synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _frontend.UpdateMedia(
                    _backend.GetMedia(_configuration.LastSync, Role.NEW_OR_CHANGED),
                    _backend.GetMediaRawData, statistics);

                WriteMessage(DateTime.Now, "Sync",
                   String.Format("Media synchronisation finished. {0} media changed.", statistics[0]));
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Media synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _frontend.UpdatePOIs(_backend.GetPOIs(_configuration.LastSync, Role.NEW_OR_CHANGED));
                WriteMessage(DateTime.Now, "Sync", "POI synchronisation finished.");
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "POI synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _backend.UpdateReviews(_frontend.GetReviews(_configuration.LastSync));
                WriteMessage(DateTime.Now, "Sync", "Review synchronisation finished.");
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Review synchronisation failed: " + e.Message);
                failed++;
            }

            try
            {
                _backend.UpdateOrders(_frontend.GetOrders(_configuration.LastSync));
                WriteMessage(DateTime.Now, "Sync", "Order synchronisation finished");
                success++;
            }
            catch (Exception e)
            {
                WriteMessage(DateTime.Now, "Error", "Order synchronisation failed: " + e.Message);
                failed++;
            }
            WriteMessage(DateTime.Now, "Sync",
                String.Format("Synchronisation finished. {0} successful, {1} failed.", success, failed));

            _frontend.Reset();
            _backend.Reset();

            #endregion SYNC_PRODUCTS
        }
        
        private void resetConfiguration()
        {
            _configuration.ReloadConfiguration();
            WriteMessage(DateTime.Now, "Configuration loaded", _configuration.ToString());

            _backend.ImageDirectory = _configuration.BackendDirectory;
            _frontend.ImageDirectory = _configuration.FrontendDirectory;
            
        }
    
        public void WriteMessage(DateTime date, string src, string message)
        {
            foreach(var logger in _loggers)
            {
                logger.WriteMessage(date,src,message);
            }
        }

        
    }
}
