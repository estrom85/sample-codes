/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

/**
 *
 * @author mato
 */
public class InvalidCardException extends Exception {
    
    private int type;
    
    /**
     * thrown if packet is full
     */
    public static final int FULL_PACKET=0;
    /**
     * thrown if program tries to put into deck null card
     */
    public static final int NULL_CARD=1;
    /**
     * thrown if program tries to put into deck invalid card
     */
    public static final int INVALID_CARD=2;
    
    
    private static final String[] messages={"Full packet", "Can't put null card", "Invalid card"};
    
    /**
     * Constructor
     * @param i type of exception thrown
     */
    InvalidCardException(int i)
    {
        super(messages[i]);
        type=i;
  
    }
    
    public int getType()
    {
        return type;
    }
}
