/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.games_interface;

import java.net.URL;
import java.util.Arrays;
import javax.swing.JCheckBox;
import javax.swing.JLabel;
import javax.swing.JSpinner;
import javax.swing.JTextField;


/**
 *
 * @author martinm
 */

/*
 * CSetting class - includes all static methods and parameters used for 
 * setting up the game
 * These settings are used on initialization of the game.
 */
public final class CSettings {
 
   
    
  /*
   * Flags -    switches on and off different setting sets for the game
   *            selected by registration 
   *            Usage: Flag1|Flag2|Flag3...
   */
 
 public static final int FLAG_PLAYERS = 1;          //allow more than one player play the game
 public static final int FLAG_MULTI_PLAYER = 1<<1;  //allow more human players on one computer
 public static final int FLAG_DIFFICULTY = 1<<2;    //allow difficulty setting
 public static final int FLAG_SIZE =1<<3;           //allow playboard size modifications
 
 
  /*
   * Settings - these parameters are used by game initialization
   */
 
 private static int  mGame=-1;              //selected game ID
 private static int mNumberOfPlayers=1;     //number of players currently selected
 private static String[]mPlayers=null;      //names of players
 private static boolean[] mComputer;        //toggles AI
 private static int mDifficulty=0;          //difficulty level
 private static int mWidth=0;               //width of playfield
 private static int mHeight=0;              //height of playfield
 private static URL codeBase;               //URL of applet
 
 /*
  * Components - used for enabling UI components for different games
  * Each game has its own settings sets so it is not necessary to enable all
  * UI components for each game 
  * These variables are also used for setting up default values and margin
  * values of each component
  */
 
 private static JLabel gameLabel;           //displays name of the game
 private static JSpinner numPlayerUI;       //sets number of Players
 private static JSpinner difficultyUI;      //sets difficulty level
 private static JSpinner widthUI;           //sets width of playfield
 private static JSpinner heightUI;          //sets height of playfield
 private static JTextField[] playersUI=new JTextField[8];   //sets names of players
 private static JCheckBox[] computerUI=new JCheckBox[7];    //toggles AI
 
 /*
  * Setting components methods - these methods are used to set up reference to
  * UI components
  */
 
 //sets component for toggle AI
 public static void setComputerUI(JCheckBox check, int i)
 {
     if(i<0||i>=computerUI.length) return;  //checks availability of index
     computerUI[i]=check;
 }
 
 //sets component for player name setting
 public static void setPlayerNameUI(JTextField text, int i)
 {
     if(i<0||i>8) return;   //checks availability of index
     playersUI[i]=text;
 }
 
 //sets component for displaying name of game
 public static void setGameLabel(JLabel label)
 {
     gameLabel=label;
 }
 
 //sets component for setting up number of players
 public static void setPlayerUI(JSpinner spinner)
 {
     numPlayerUI=spinner;
 }
 
 //sets component for setting up difficulty level
 public static void setDifficultyUI(JSpinner spinner)
 {
     difficultyUI=spinner;
 }
 
 //sets component for setting up width of playfield
 public static void setWidthUI(JSpinner spinner)
 {
     widthUI=spinner;
 }
 
 //sets component for setting up height of playfield
 public static void setHeightUI(JSpinner spinner)
 {
     heightUI=spinner;
 }
 
 //enables and sets values of components
 public static void setComponents()
 {
     
     if(!CGames.isValid(mGame)) return;    //checks if game is registered
     
     if(gameLabel!=null)                   //sets name of game
         gameLabel.setText(getName());
     
     if(numPlayerUI!=null)                  //sets number of players
     {
         //checks if modifying of number of players is valid
         if(CGames.validateSetting(mGame, FLAG_PLAYERS)) 
         {
             numPlayerUI.setEnabled(true);
             numPlayerUI.setValue(mNumberOfPlayers);
         }
             
         else
             numPlayerUI.setEnabled(false);
     }
     
     if(difficultyUI!=null)                 //sets difficulty level
     {
         //checks if modifying of difficulty level is valid
         if(CGames.validateSetting(mGame, FLAG_DIFFICULTY))
         {
             difficultyUI.setEnabled(true);
             difficultyUI.setValue(mDifficulty);
         }
         else
             difficultyUI.setEnabled(false);
     }
     
     if(widthUI!=null)                      //sets width of playfield
     {
         //checks if modifying of playfield size is valid
         if(CGames.validateSetting(mGame, FLAG_SIZE))
         {
             widthUI.setEnabled(true);
             widthUI.setValue(mWidth);
         }
         else
             widthUI.setEnabled(false);
     }
     
     if(heightUI!=null)                     //sets height of playfield
     {
         //checks if modifying of playfied size is valid
         if(CGames.validateSetting(mGame, FLAG_SIZE))
         {
             heightUI.setEnabled(true);
             heightUI.setValue(mHeight);
         }
         else
             heightUI.setEnabled(false);
     }
     
     for (int i=0;i<8;i++)                  //sets player names
     {
         if(playersUI[i]==null) continue;   //if component does not exists check another field
         
         if(i<mNumberOfPlayers)             //set player name if name exists else set default name
         {
             playersUI[i].setText(mPlayers[i]);
             playersUI[i].setEnabled(true);
         }
         else
         {
             playersUI[i].setText("Hrac "+(i+1));
             playersUI[i].setEnabled(false);
         }
     }
     for(int i=0;i<7;i++)                   //sets AI
     {
         if(computerUI[i]==null) continue;  //if component does not exists check another field
         
         //toggles computer AI if the setting is valid
         if(i<mNumberOfPlayers-1&&CGames.validateSetting(mGame, FLAG_MULTI_PLAYER))
         {
             computerUI[i].setSelected(mComputer[i+1]);
             computerUI[i].setEnabled(true);
         }
         else
         {
             computerUI[i].setSelected(true);
             computerUI[i].setEnabled(false);
         }
     }
         
 }
 
 //sets URL of applet
 public static void setURL(URL url)
 {
     codeBase=url;
 }
 
 //returns URL of applet
 public static URL getURL()
 {
     return codeBase;
 }
 
 //sets default settings - minimum possible settings
 public static void defaultSettings()
 {
     if(!CGames.isValid(mGame)) return;
     
     setNumberOfPlayers(CGames.getMargins(mGame, CGames.MIN_PLAYERS));
     setDifficulty(CGames.getMargins(mGame, CGames.MIN_DIFF));
     setSize(CGames.getMargins(mGame, CGames.MIN_WIDTH),CGames.getMargins(mGame, CGames.MIN_HEIGHT));
 }
 
 //selects game
 public static void setGame(int gameID)
 {
     if(CGames.isValid(gameID))     //checks if game is valid
         mGame=gameID;
     defaultSettings();             //sets default settings for selected
     setComponents();               //sets components for selected game
 }
 
 //starts selected game
 public static void startGame()
 {
     if(CGames.isValid(mGame))
         CGameManager.setGame(CGames.createGame(mGame));
 }
 
 //sets number of players
 public static void setNumberOfPlayers(int n)
 {
     if(!CGames.isValid(mGame)) return; //checks if game is valid
     
     //copies names of players and AI switches into temporary fields
     String[] temp=null;
     if(mPlayers!=null)
        temp=Arrays.copyOf(mPlayers, mPlayers.length);
     
     boolean[] tempComputer=null;
     if(mComputer!=null)
         tempComputer=Arrays.copyOf(mComputer, mComputer.length);
     
     //if changing of number of player is not valid set default settings
     if(!CGames.validateSetting(mGame, FLAG_PLAYERS))
     {
         mNumberOfPlayers=1;
         mPlayers=null;
         mPlayers=new String[1];
         mPlayers[0]="Hrac";
         mComputer=null;
         mComputer=new boolean[1];
         mComputer[0]=false;
         return;
     }
     
     //checks for marginal values
     if(n<CGames.getMargins(mGame, CGames.MIN_PLAYERS))
         mNumberOfPlayers=CGames.getMargins(mGame, CGames.MIN_PLAYERS);
     else if(n>CGames.getMargins(mGame, CGames.MAX_PLAYERS))
         mNumberOfPlayers=CGames.getMargins(mGame, CGames.MAX_PLAYERS);
     else        
         mNumberOfPlayers=n;
     
     //sets new players
     mPlayers=null;
     mPlayers=new String[mNumberOfPlayers];
     mComputer=null;
     mComputer=new boolean[mNumberOfPlayers];
         
     for (int i=0;i<mNumberOfPlayers;i++)
     {
         //copies from temp array if temp length is lower then new array rest of 
         //the values are default
         if(temp!=null&&i<temp.length)
            mPlayers[i]=temp[i];
         else
            mPlayers[i]="Hrac "+(i+1);
           
         
         if(i==0)
              mComputer[i]=false; //first player is always human
         else
         {
             //checks if setting AI is valid
             if(CGames.validateSetting(mGame, FLAG_MULTI_PLAYER)) 
             {
                 if(tempComputer!=null&&i<tempComputer.length)
                     mComputer[i]=tempComputer[i];
                 else
                     mComputer[i]=false;  
              }                   
              else
                 mComputer[i]=true;
          }          
     }
     setComponents();    
     }
     
 //sets name of player
 public static void setPlayerName(int index, String name)
 {
     if(!CGames.isValid(mGame)) return;   //checks if game is valid
     
     if(index<0||index>=mPlayers.length) return;    //checks if index is valid
     
     if(mPlayers[index]==null) return;  //checks if object exists
     
     mPlayers[index]=name;  
     
     setComponents();
 }
 
 //toggles game AI
 public static void toggleComputer(int n)
 {
     if(!CGames.isValid(mGame)) return;   //checks if game is valid
     
     //checks if setting is valid
     if(!CGames.validateSetting(mGame, FLAG_MULTI_PLAYER))
         return;
     if(n<1||n>=mComputer.length) return;
     
     mComputer[n]=!mComputer[n];
     
     setComponents();
 }
 
 //sets difficulty level
 public static void setDifficulty(int difficulty)
 {
     if(!CGames.isValid(mGame)) return;   //checks if game is valid
     
     //checks if setting is valid
     if(!CGames.validateSetting(mGame, FLAG_DIFFICULTY))
         return;
     
     //checks for marginal values
     if(difficulty<CGames.getMargins(mGame, CGames.MIN_DIFF))
         mDifficulty=CGames.getMargins(mGame, CGames.MIN_DIFF);
     else if(difficulty>CGames.getMargins(mGame, CGames.MAX_DIFF))
         mDifficulty=CGames.getMargins(mGame, CGames.MAX_DIFF);
     else
         mDifficulty=difficulty;
     
     setComponents();
 }
 
 //sets size of playfield
 public static void setSize(int width, int height)
 {
     
     if(!CGames.isValid(mGame)) return;    //checks if game is valid
     //checks if setting is valid
     if(!CGames.validateSetting(mGame, FLAG_SIZE))
         return;
     
     //checks for marginal values
     if(width<CGames.getMargins(mGame, CGames.MIN_WIDTH))
         mWidth=CGames.getMargins(mGame, CGames.MIN_WIDTH);
     else if(width>CGames.getMargins(mGame, CGames.MAX_WIDTH))
         mWidth=CGames.getMargins(mGame, CGames.MAX_WIDTH);
     else
         mWidth=width;
     
     if(height<CGames.getMargins(mGame, CGames.MIN_HEIGHT))
         mHeight=CGames.getMargins(mGame, CGames.MIN_HEIGHT);
     else if(height>CGames.getMargins(mGame, CGames.MAX_HEIGHT))
         mHeight=CGames.getMargins(mGame, CGames.MAX_HEIGHT);
     else
         mHeight=height;
     
     setComponents();
     
 }
 
 /*
  * return statements
  */
 public static int getNumberOfPlayers()
 {
     return mNumberOfPlayers;
 }
 
 public static String getPlayerName(int i)
 {
     if (i<0||i>mPlayers.length-1) //checks for valid index
         return "";
     return mPlayers[i];
 }
 
 public static boolean getComputer(int i)
 {
     if(i<0||i>mComputer.length) return false;
     return mComputer[i];
 }
 
 public static int getDifficulty()
 {
     return mDifficulty;
 }
 
 public static int getWidth()
 {
     return mWidth;
 }
 
 public static int getHeight()
 {
     return mHeight;
 }
 
 public static String getName()
 {
     return CGames.getName(mGame);
 }
 
 private CSettings(){}
}
