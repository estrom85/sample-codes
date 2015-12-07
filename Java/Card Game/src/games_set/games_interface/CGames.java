/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.games_interface;

import games_set.card_games32.sedma3.CSedma3;

/**
 *
 * @author martinm
 */
public final class CGames {
    
 /*
  * Here comes IDs and names of games. If new game will be made,
  * this class need to be modified
  */
    private static final int ID=0;
    private static final int FLAGS=1;
    public static final int MIN_PLAYERS=2;
    public static final int MAX_PLAYERS=3;
    public static final int MIN_DIFF=4;
    public static final int MAX_DIFF=5;
    public static final int MIN_WIDTH=6;
    public static final int MAX_WIDTH=7;
    public static final int MIN_HEIGHT=8;
    public static final int MAX_HEIGHT=9;
    
    /* 
     * Game ID - must be different from -1
     */
    public static final int Sedma3=0;
    
    /*
     * Game registration - program will not know the game exists
     */
    
    /*
     * {GameID,
     * Settings flags, 
     * minPlayers,
     * maxPlayers, 
     * minDifficulty, 
     * maxDifficulty,
     * minWidth,
     * maxWidth,
     * minHeight,
     * maxHeight}
     */
    
    private static final int[][] games=
    {
        {Sedma3,CSettings.FLAG_PLAYERS,2,6,1,5,1,3,5,8} 
    };
    
    /*
     * Name of the game
     */
    private static final String[] gameNames=
    {
        "3 hore"
    };
    
    /*
     * Settings flags (what kind of settings are available for the game)
     */
    
    public static CGame createGame(int game)
    {
        CGame result=null;
        switch(game)
        {
            case Sedma3:
                result=new CSedma3();
        }
        return result;
    }
    
    /*
     * other public methods - please do not modify
     */
    
    public static boolean isValid(int gameID)
    {
        boolean result=false;
        for (int i=0;i<games.length;i++)
        {
            if(gameID==games[i][0]&&games[i].length==10)
            {
                result=true;
                break;
            }
        }
        return result;
    }
    
    public static boolean validateSetting(int gameID,int flag)
    {
        int bit=0;
        
        //checks, if argument flag has only one Setting flag
        for (int i=0;i<7;i++)
        {
            if((flag&(1<<i))!=0) bit++;
        }
        if (bit!=1) return false;
        
        boolean result=false;
        for(int i=0;i<games.length;i++)
        {
            if(gameID==games[i][ID])
            {
                if((flag&games[i][FLAGS])!=0)
                {
                    result = true;
                }
                break;
            }
        }
        return result;
    }
    
    public static int numberOfGames()
    {
        return games.length;
    }
    
    public static int getMargins(int game, int margin)
    {
        if (margin<2||margin>9) return 0; //checks for valid margin type
        
        for (int i=0;i<games.length;i++) //look for game id
        {
            if(games[i][ID]==game)
                return games[i][margin];
        }
        
        return 0;
        
    }
    
    public static String getName(int game)
    {
        for (int i=0;i<games.length;i++)
        {
            if(games[i][ID]==game)
                return gameNames[i];
        }
        return "";
    }
    
    private CGames(){}

}