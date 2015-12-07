using Logging;
using SyncApp;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.ServiceProcess;
using System.Text;
using System.Threading.Tasks;

namespace SyncService
{
    public partial class SyncService : ServiceBase, ILogger
    {
        private SyncApplication _application;
        public SyncService()
        {
            InitializeComponent();
            
            _application = new SyncApplication();
            _application.SubscribeLogger(this);
        }

        protected override void OnStart(string[] args)
        {
            WriteMessage(DateTime.Now, "WinService", "service started");
            _application.StartService();
        }

        protected override void OnStop()
        {
            _application.StopService();
            WriteMessage(DateTime.Now, "WinService", "service stopped");      
        }

        public void WriteMessage(DateTime date, string src, string message)
        {
            string msg = String.Format("[{0} - {1}]:{2}\r\n",date.ToShortDateString(),date.ToShortTimeString(),message);
            //eventLog1.WriteEntry(message);
            File.AppendAllText(_application.GetHomeDirectory()+@"\"+src+".log",msg);
        }

        
    }
}
