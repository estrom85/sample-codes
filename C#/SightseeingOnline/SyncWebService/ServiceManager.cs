using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ServiceModel;
using DataProvider.Providers;
using Logging;

namespace SyncWebService
{
    public class ServiceManager
    {
        private ServiceHost _host;
        private Dictionary<Guid,Session> _sessions = new Dictionary<Guid,Session>();
        private ILogger _logger;
        
        public BackendDataProvider DataProvider
        {
            set
            {
                SyncService.DataProvider = value;
            }
            get
            {
                return SyncService.DataProvider;
            }
        }

        public TimeSpan SessionTimeout
        {
            set
            {
                SyncService.SessionTimeout = (int)value.TotalMilliseconds;
            }
        }

        private ServiceManager(ILogger logger)
        {
            SyncService.Sessions = _sessions;
            SyncService.SessionTimeout = 1800000;
            SyncService.Logger = logger;
            _logger = logger;
            _host = new ServiceHost(typeof(SyncService));
            _logger.WriteMessage(DateTime.Now,"Webservice","Service host created. Service name: "+_host.Description.Name+
                ". Number of endpoints: "+_host.Description.Endpoints.Count);
        }
        
        private static ServiceManager _instance;
        public static ServiceManager GetInstance(ILogger logger)
        {
            if (_instance == null)
            {
                _instance = new ServiceManager(logger);
            }
            return _instance;
        }

        public void Start() 
        {
            _host.Open();

            _logger.WriteMessage(DateTime.Now, "Webservice", "Service host opened.");
        }

        public void Stop() 
        {
            _host.Close();

            _logger.WriteMessage(DateTime.Now, "Webservice", "Service host closed.");
        }
    }
}
